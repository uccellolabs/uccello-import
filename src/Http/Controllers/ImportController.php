<?php

namespace Uccello\Import\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;
use Uccello\Core\Http\Controllers\Core\Controller;
use Uccello\Core\Models\Domain;
use Uccello\Core\Models\Module;
use Uccello\Import\Imports\GenericImport;
use Uccello\Import\Jobs\NotifyUserOfCompletedImport;
use Uccello\Import\Models\ImportMapping;

class ImportController extends Controller
{
    /**
     * Check user permissions
     */
    protected function checkPermissions()
    {
        $this->middleware('uccello.permissions:create');
    }

    /**
     * Process and display asked page
     * @param Domain|null $domain
     * @param Module $module
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function prepare(?Domain $domain, Module $module, Request $request)
    {
        // Pre-process
        $this->preProcess($domain, $module, $request);

        $domain = $this->domain;

        $headings = [];
        $fullPath = null;

        if (request()->file('file')) {
            // Make directory path
            $directoryPath = isset($domain) ? $domain->slug.'/' : ''; // Domain
            $directoryPath .= 'import'; // Custom directory
            $directoryPath = trim($directoryPath, '/');

            // Save file
            $path = Storage::putFile($directoryPath, request()->file('file'));
            $filePath = storage_path('app/' . $path);

            // Get headings
            $readerType = request()->file('file')->getMimeType() === 'text/plain' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX;
            $headings = (new HeadingRowImport(1))->toArray($filePath, null, $readerType);
            if (!empty($headings[0][0])) { // First Sheet
                $headings = $headings[0][0];
            }

            $firstRow = (new HeadingRowImport(2))->toArray($filePath, null, $readerType);
            if (!empty($firstRow[0][0])) {
                $firstRow = $firstRow[0][0];
            }

            // Get all mapping for this module
            $importMappings = ImportMapping::where('module_id', $this->module->id)
                ->whereNull('user_id')
                ->orWhere('user_id', auth()->id())
                ->orderBy('name')
                ->get();
        }

        return view('import::modules.import.main', compact('headings', 'firstRow', 'filePath', 'importMappings'));
    }

    /**
     * Process and display asked page
     * @param Domain|null $domain
     * @param Module $module
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function process(?Domain $domain, Module $module, Request $request)
    {
        // Pre-process
        $this->preProcess($domain, $module, $request);

        $domain = $this->domain;

        // Save mapping
        $this->__saveMapping($request);

        (new GenericImport($domain, $module, $request->fields, $request->defaults))->queue($request->filepath)->chain([
            new NotifyUserOfCompletedImport(auth()->user(), uctrans($module->name, $module)),
        ]);

        // TODO: Add flash info (Import launched).

        return redirect(ucroute('uccello.list', $domain, $module));
    }

    public function fieldConfig(?Domain $domain, Module $module, Request $request)
    {
        // Pre-process
        $this->preProcess($domain, $module, $request);

        $fieldName = $request->field;

        $field = $module->fields->where('name', $fieldName)->first();

        // If a special template exists, use it. Else use the generic template
        $uitype = uitype($field->uitype_id);
        $uitypeViewName = sprintf('uitypes.import.%s', $uitype->name);
        $uitypeFallbackView = 'import::modules.default.uitypes.import.'.$uitype->name;
        $uitypeViewToInclude = uccello()->view($module->package, $module, $uitypeViewName, $uitypeFallbackView);

        // If view does not exist, use the text uitype view
        if (!view()->exists($uitypeViewToInclude)) {
            $uitypeViewToInclude = 'import::modules.default.uitypes.import.text';
        }

        return view()->make($uitypeViewToInclude, compact('domain', 'module', 'field'))->render();
    }

    /**
     * Create or update a mapping
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function __saveMapping(Request $request)
    {
        // Save only if a name is defined
        if (!$request->mapping_name) {
            return;
        }

        // Generate mapping config
        $config = [];
        foreach ($request->fields as $i => $field) {
            $config[] = [
                'field' => $field,
                'default' => !empty($request->defaults[$i]) ? $request->defaults[$i] : null
            ];
        }

        // Create or update mapping
        $importMapping = ImportMapping::firstOrNew([
            'module_id' => $this->module->id,
            'user_id' => auth()->id(),
            'name' => $request->mapping_name
        ]);

        $importMapping->config = $config;
        $importMapping->save();
    }
}
