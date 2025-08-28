/**
 * zone99_SARSymbol RequireJS Configuration
 * 
 * @category  zone99
 * @package   zone99_SARSymbol
 * @author    Saudi Riyal Currency Extension
 * @copyright Copyright (c) 2024
 */

var config = {
    paths: {
        'saudi-riyal-converter': 'zone99_SARSymbol/js/currency-converter',
        'saudi-riyal-price-updater': 'zone99_SARSymbol/js/price-updater'
    },
    shim: {
        'saudi-riyal-converter': {
            deps: ['jquery']
        },
        'saudi-riyal-price-updater': {
            deps: ['jquery', 'saudi-riyal-converter']
        }
    },
    deps: [
        'saudi-riyal-converter'
    ],
    map: {
        '*': {
            'saudiRiyalConverter': 'saudi-riyal-converter',
            'saudiRiyalPriceUpdater': 'saudi-riyal-price-updater'
        }
    }
}; 