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
        }

        return view('import::modules.import.main', compact('headings', 'firstRow', 'filePath'));
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
        (new GenericImport($module, $request->fields, $request->defaults))->queue($request->filepath)->chain([
            new NotifyUserOfCompletedImport(auth()->user(), uctrans($module->name, $module)),
        ]);

        // Pre-process
        $this->preProcess($domain, $module, $request);

        $domain = $this->domain;

        // TODO: Add flash info (Import launched).

        return redirect(ucroute('uccello.list', $domain, $module));
    }
}
