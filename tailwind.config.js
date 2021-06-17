module.exports = {
    purge: [
        './resources/**/*.blade.php',
    ],
    darkMode: false, // or 'media' or 'class'
    theme: {
        extend: {
            colors: {
                primary: {
                    500: '#007ADD', // Blue Uccello
                    900: '#0B2540', // Blue text
                },
                blue: {
                    backgroundIcon: '#ebf5fc', // couleur de fond des icons
                },
                orange: {
                    100: '#F9EBE8',
                    500: '#FC6534',
                },
                green: {
                    500: '#28CA90',
                },
                purple: {
                    500: '#7E54EF',
                },
                red: {
                    500: '#CA2828',
                },
            },
        }
    },
    variants: {
        extend: {},
    },
    plugins: [],
}
