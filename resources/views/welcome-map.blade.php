<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Pest Control Personnel | Professional Pest Control Network</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script src="https://d3js.org/topojson.v3.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .province {
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            stroke: #ffffff;
            stroke-width: 2;
            stroke-linejoin: round;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.1));
        }

        .province:hover {
            opacity: 0.85;
            stroke-width: 3;
            stroke: #667eea;
            filter: drop-shadow(0 4px 6px rgba(102, 126, 234, 0.3));
            transform: translateY(-2px);
        }

        .province.selected {
            stroke: #764ba2;
            stroke-width: 4;
            filter: drop-shadow(0 8px 16px rgba(118, 75, 162, 0.4));
        }

        .province-label {
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            font-weight: 600;
            fill: #1e293b;
            pointer-events: none;
            text-anchor: middle;
            text-shadow: 0 1px 3px rgba(255, 255, 255, 0.9);
        }

        .service-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .service-card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
            border-color: rgba(102, 126, 234, 0.3);
        }

        #map-container {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #dbeafe 100%);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .legend-color {
            width: 30px;
            height: 20px;
            border-radius: 4px;
            margin-right: 10px;
            border: 1px solid #ccc;
        }

        #search-results {
            animation: fadeIn 0.2s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .search-result-item {
            transition: all 0.2s ease;
        }

        .search-result-item:hover {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-screen">
<div class="container mx-auto px-4 py-12">
    <!-- Header -->
    <div class="text-center mb-12">
        <div class="inline-block mb-4">
            <span class="badge">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Trusted Professional Network
            </span>
        </div>
        <h1 class="text-6xl font-bold text-white mb-4 tracking-tight">
            Find Your Pest Control Personnel
        </h1>
        <p class="text-white text-xl max-w-2xl mx-auto leading-relaxed opacity-90">
            Connect with certified pest control experts in your area. Professional, reliable, and ready to help.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Map Container -->
        <div class="lg:col-span-3">
            <div id="map-container" class="relative p-6">
                <!-- Loading Indicator -->
                <div id="loading-indicator" class="absolute inset-0 flex items-center justify-center z-50 bg-white bg-opacity-90 rounded-xl">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500 mb-4"></div>
                        <p class="text-gray-800 text-lg font-semibold">Loading Netherlands Map...</p>
                        <p class="text-gray-600 text-sm mt-2">Fetching province boundaries...</p>
                    </div>
                </div>

                <!-- Map SVG will be inserted here -->
                <div id="map-svg-container" class="w-full" style="min-height: 700px;"></div>

                <!-- Legend -->
                <div class="absolute bottom-6 left-6 glass-effect p-5 rounded-2xl shadow-2xl">
                    <h3 class="font-bold text-gray-800 mb-3 text-sm uppercase tracking-wide">Personnel Availability</h3>
                    <div class="legend-item">
                        <div class="legend-color" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"></div>
                        <span class="text-sm text-gray-700 font-medium">1-2 specialists</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"></div>
                        <span class="text-sm text-gray-700 font-medium">3-4 specialists</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);"></div>
                        <span class="text-sm text-gray-700 font-medium">5+ specialists</span>
                    </div>
                </div>

                <!-- Province Info Tooltip -->
                <div id="province-tooltip" class="absolute hidden glass-effect px-5 py-3 rounded-xl shadow-2xl z-50 border-2 border-purple-200" style="pointer-events: none;">
                    <div id="tooltip-content"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="glass-effect rounded-2xl p-6 shadow-2xl sticky top-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold gradient-text mb-2">Find Your Location</h2>
                    <p class="text-gray-600 text-sm">Search your town to discover local pest control experts</p>
                </div>

                <!-- Town Search -->
                <div class="mb-8 relative">
                    <div class="relative">
                        <input
                            type="text"
                            id="town-search"
                            placeholder="Enter your town name..."
                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 text-gray-800 placeholder-gray-400 shadow-sm"
                            autocomplete="off"
                        />
                        <svg class="absolute right-4 top-4 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <!-- Search Results Dropdown -->
                    <div id="search-results" class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-2xl hidden max-h-64 overflow-y-auto">
                        <!-- Results will be populated here -->
                    </div>
                </div>

                <div class="border-t-2 border-gray-100 pt-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Province Details
                    </h3>
                </div>

                <div id="no-selection" class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">Select a province on the map or search for your town to view available pest control personnel</p>
                </div>

                <div id="province-details" style="display: none;">
                    <div class="mb-6 p-5 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl border border-purple-100">
                        <h3 id="selected-province-name" class="text-2xl font-bold gradient-text mb-4"></h3>

                        <!-- Personnel Count - Prominent Display -->
                        <div class="mb-3 p-4 bg-white rounded-lg shadow-sm border-2 border-purple-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center mr-3">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Available Personnel</p>
                                        <p class="text-2xl font-bold text-purple-600" id="selected-service-count"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Population Info - Secondary -->
                        <div class="flex items-center text-gray-600 text-sm">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span id="selected-province-population" class="font-medium"></span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-800 flex items-center border-b-2 border-purple-100 pb-3">
                            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Personnel Directory
                        </h4>

                        <div id="services-list" class="space-y-4 max-h-96 overflow-y-auto pr-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Application state
    const app = {
        provinces: @json($provinces),
        selectedProvince: null,
        svg: null,
        projection: null,
        path: null,
        searchTimeout: null
    };

    console.log('Province data loaded:', app.provinces);

    // Town search functionality
    const townSearch = document.getElementById('town-search');
    const searchResults = document.getElementById('search-results');

    townSearch.addEventListener('input', function(e) {
        const query = e.target.value.trim();

        clearTimeout(app.searchTimeout);

        if (query.length < 2) {
            searchResults.classList.add('hidden');
            searchResults.innerHTML = '';
            return;
        }

        app.searchTimeout = setTimeout(() => {
            fetch(`/api/towns/search?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(towns => {
                    displaySearchResults(towns);
                })
                .catch(error => {
                    console.error('Search error:', error);
                });
        }, 300);
    });

    function displaySearchResults(towns) {
        if (towns.length === 0) {
            searchResults.innerHTML = '<div class="p-4 text-gray-500 text-sm text-center">No towns found</div>';
            searchResults.classList.remove('hidden');
            return;
        }

        const html = towns.map(town => `
            <div class="search-result-item p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0"
                 data-province="${town.province_name}"
                 onclick="selectTown('${town.name}', '${town.province_name}')">
                <div class="font-semibold text-gray-800">${town.name}</div>
                <div class="text-xs text-gray-500 flex items-center justify-between mt-1">
                    <span>${town.province_name}</span>
                    <span>${town.population ? town.population.toLocaleString() + ' residents' : ''}</span>
                </div>
            </div>
        `).join('');

        searchResults.innerHTML = html;
        searchResults.classList.remove('hidden');
    }

    function selectTown(townName, provinceName) {
        // Update search input
        townSearch.value = townName;

        // Hide search results
        searchResults.classList.add('hidden');

        // Find and select the province on the map
        const provinceElement = document.querySelector(`.province[data-province="${provinceName}"]`);
        if (provinceElement) {
            selectProvince(provinceName, provinceElement);

            // Optionally scroll the map into view if needed
            provinceElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!townSearch.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });

    // Province name mappings (handles different naming conventions in GeoJSON)
    const provinceNameMap = {
        'Noord-Holland': ['Noord-Holland', 'North Holland', 'NH'],
        'Zuid-Holland': ['Zuid-Holland', 'South Holland', 'ZH'],
        'Utrecht': ['Utrecht', 'UT'],
        'Zeeland': ['Zeeland', 'ZL'],
        'Noord-Brabant': ['Noord-Brabant', 'North Brabant', 'NB'],
        'Limburg': ['Limburg', 'LI'],
        'Gelderland': ['Gelderland', 'GE'],
        'Overijssel': ['Overijssel', 'OV'],
        'Drenthe': ['Drenthe', 'DR'],
        'Groningen': ['Groningen', 'GR'],
        'Friesland': ['Friesland', 'Fryslân', 'FR'],
        'Flevoland': ['Flevoland', 'FL']
    };

    function matchProvinceName(geoName) {
        for (const [standardName, variants] of Object.entries(provinceNameMap)) {
            if (variants.some(v => geoName.toLowerCase().includes(v.toLowerCase()) || v.toLowerCase().includes(geoName.toLowerCase()))) {
                return standardName;
            }
        }
        return geoName;
    }

    function getProvinceColor(serviceCount) {
        if (serviceCount <= 2) return '#10b981'; // Green
        if (serviceCount <= 4) return '#3b82f6'; // Blue
        return '#8b5cf6'; // Purple
    }

    // Embedded Netherlands provinces GeoJSON data with connected boundaries
    // Provinces share exact border coordinates to form a complete map
    function getEmbeddedGeoJSON() {
        return {
            "type": "FeatureCollection",
            "features": [
                {
                    "type": "Feature",
                    "properties": { "name": "Groningen" },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [6.76, 53.47], [7.22, 53.47], [7.22, 53.25], [7.21, 53.15],
                            [7.05, 53.08], [6.90, 53.00], [6.60, 53.00], [6.35, 53.08],
                            [6.20, 53.15], [6.20, 53.28], [6.35, 53.40], [6.60, 53.47], [6.76, 53.47]
                        ]]
                    }
                },
                {
                    "type": "Feature",
                    "properties": { "name": "Friesland" },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [5.35, 53.47], [6.60, 53.47], [6.35, 53.40], [6.20, 53.28],
                            [6.20, 53.15], [6.35, 53.08], [6.60, 53.00], [6.20, 53.00],
                            [5.80, 52.85], [5.40, 52.85], [5.10, 53.00], [4.85, 53.10],
                            [4.75, 53.30], [5.00, 53.47], [5.35, 53.47]
                        ]]
                    }
                },
                {
                    "type": "Feature",
                    "properties": { "name": "Drenthe" },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [6.20, 53.00], [6.60, 53.00], [6.90, 53.00], [6.90, 52.50],
                            [6.75, 52.50], [6.60, 52.55], [6.35, 52.65], [6.20, 52.75],
                            [6.20, 53.00]
                        ]]
                    }
                },
                {
                    "type": "Feature",
                    "properties": { "name": "Overijssel" },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [5.80, 52.85], [6.20, 53.00], [6.20, 52.75], [6.35, 52.65],
                            [6.60, 52.55], [6.75, 52.50], [6.90, 52.50], [6.90, 52.25],
                            [6.75, 52.10], [6.55, 52.00], [6.25, 52.00], [6.05, 52.10],
                            [5.85, 52.25], [5.75, 52.40], [5.75, 52.65], [5.80, 52.85]
                        ]]
                    }
                },
                {
                    "type": "Feature",
                    "properties": { "name": "Flevoland" },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [5.10, 53.00], [5.40, 52.85], [5.80, 52.85], [5.75, 52.65],
                            [5.75, 52.40], [5.65, 52.30], [5.40, 52.30], [5.25, 52.40],
                            [5.20, 52.60], [5.10, 52.80], [5.10, 53.00]
                        ]]
                    }
                },
                {
                    "type": "Feature",
                    "properties": { "name": "Noord-Holland" },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [4.55, 53.47], [5.00, 53.47], [4.75, 53.30], [4.85, 53.10],
                            [5.10, 53.00], [5.10, 52.80], [5.20, 52.60], [5.25, 52.40],
                            [5.15, 52.30], [4.90, 52.30], [4.70, 52.35], [4.55, 52.45],
                            [4.45, 52.60], [4.42, 52.85], [4.45, 53.10], [4.50, 53.30], [4.55, 53.47]
                        ]]
                    }
                },
                {
                    "type": "Feature",
                    "properties": { "name": "Utrecht" },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [4.90, 52.30], [5.15, 52.30], [5.25, 52.40], [5.40, 52.30],
                            [5.65, 52.30], [5.60, 52.10], [5.55, 52.00], [5.35, 51.95],
                            [5.10, 51.95], [4.95, 52.00], [4.90, 52.15], [4.90, 52.30]
                        ]]
                    }
                },
                {
                    "type": "Feature",
                    "properties": { "name": "Gelderland" },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [5.65, 52.30], [5.75, 52.40], [5.85, 52.25], [6.05, 52.10],
                            [6.25, 52.00], [6.55, 52.00], [6.75, 52.10], [6.90, 52.25],
                            [6.90, 52.00], [6.75, 51.85], [6.55, 51.75], [6.25, 51.75],
                            [6.00, 51.80], [5.75, 51.85], [5.55, 51.95], [5.60, 52.10], [5.65, 52.30]
                        ]]
                    }
                },
                {
                    "type": "Feature",
                    "properties": { "name": "Zuid-Holland" },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [3.85, 52.20], [4.70, 52.35], [4.90, 52.30], [4.90, 52.15],
                            [4.95, 52.00], [5.10, 51.95], [5.10, 51.80], [4.90, 51.75],
                            [4.60, 51.70], [4.30, 51.65], [4.05, 51.70], [3.85, 51.85],
                            [3.85, 52.20]
                        ]]
                    }
                },
                {
                    "type": "Feature",
                    "properties": { "name": "Zeeland" },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [3.35, 51.65], [4.05, 51.70], [4.30, 51.65], [4.30, 51.50],
                            [4.15, 51.38], [3.85, 51.35], [3.55, 51.35], [3.35, 51.42], [3.35, 51.65]
                        ]]
                    }
                },
                {
                    "type": "Feature",
                    "properties": { "name": "Noord-Brabant" },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [4.30, 51.65], [4.60, 51.70], [4.90, 51.75], [5.10, 51.80],
                            [5.35, 51.95], [5.55, 51.95], [5.75, 51.85], [5.85, 51.70],
                            [5.85, 51.55], [5.70, 51.45], [5.45, 51.38], [5.10, 51.38],
                            [4.75, 51.38], [4.50, 51.42], [4.30, 51.50], [4.30, 51.65]
                        ]]
                    }
                },
                {
                    "type": "Feature",
                    "properties": { "name": "Limburg" },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [5.75, 51.85], [6.00, 51.80], [6.25, 51.75], [6.15, 51.55],
                            [6.10, 51.35], [6.05, 51.10], [6.00, 50.85], [5.95, 50.75],
                            [5.85, 50.75], [5.78, 50.85], [5.75, 51.05], [5.75, 51.30],
                            [5.78, 51.50], [5.85, 51.70], [5.75, 51.85]
                        ]]
                    }
                }
            ]
        };
    }

    async function initMap() {
        try {
            console.log('Using embedded GeoJSON data for Netherlands provinces');

            // Use embedded GeoJSON data
            const geoData = getEmbeddedGeoJSON();

            console.log('GeoJSON loaded, features:', geoData.features.length);

            renderMap(geoData);
            document.getElementById('loading-indicator').style.display = 'none';

        } catch (error) {
            console.error('Error loading map:', error);
            document.getElementById('loading-indicator').innerHTML = `
                <div class="text-center">
                    <div class="text-red-500 text-xl mb-4">⚠️ Failed to load map data</div>
                    <p class="text-gray-600 text-sm">${error.message}</p>
                    <p class="text-gray-600 text-sm mt-2">Please refresh the page.</p>
                </div>
            `;
        }
    }

    function renderMap(geoData) {
        const container = document.getElementById('map-svg-container');
        const width = container.clientWidth;
        const height = 700;

        // Create SVG
        app.svg = d3.select('#map-svg-container')
            .append('svg')
            .attr('width', width)
            .attr('height', height)
            .attr('viewBox', `0 0 ${width} ${height}`)
            .style('background', 'transparent');

        // Create projection centered on Netherlands
        app.projection = d3.geoMercator()
            .center([5.5, 52.2]) // Center of Netherlands
            .scale(7000)
            .translate([width / 2, height / 2]);

        app.path = d3.geoPath().projection(app.projection);

        // Draw provinces
        const provinces = app.svg.selectAll('.province')
            .data(geoData.features)
            .enter()
            .append('g')
            .attr('class', 'province-group');

        provinces.append('path')
            .attr('class', 'province')
            .attr('d', app.path)
            .attr('fill', d => {
                const provinceName = matchProvinceName(getFeatureName(d));
                const data = app.provinces[provinceName];
                const serviceCount = data?.pestControlServices?.length || 0;
                return getProvinceColor(serviceCount);
            })
            .attr('data-province', d => matchProvinceName(getFeatureName(d)))
            .on('click', function(event, d) {
                const provinceName = matchProvinceName(getFeatureName(d));
                selectProvince(provinceName, this);
            })
            .on('mouseover', function(event, d) {
                const provinceName = matchProvinceName(getFeatureName(d));
                showTooltip(event, provinceName);
            })
            .on('mousemove', function(event) {
                moveTooltip(event);
            })
            .on('mouseout', function() {
                hideTooltip();
            });

        // Add province labels
        provinces.append('text')
            .attr('class', 'province-label')
            .attr('transform', d => {
                const centroid = app.path.centroid(d);
                return `translate(${centroid[0]}, ${centroid[1]})`;
            })
            .text(d => {
                const name = getFeatureName(d);
                return matchProvinceName(name);
            })
            .style('font-size', '11px')
            .style('font-weight', 'bold');

        console.log('Map rendered successfully');
    }

    function getFeatureName(feature) {
        const props = feature.properties;
        return props.name || props.NAME || props.statnaam || props.provincienaam ||
               props.province || props.PROVINCE || props.admin || 'Unknown';
    }

    function selectProvince(provinceName, element) {
        console.log('Province selected:', provinceName);

        // Remove previous selection
        d3.selectAll('.province').classed('selected', false);

        // Highlight selected province
        d3.select(element).classed('selected', true);

        // Update sidebar
        const provinceData = app.provinces[provinceName];
        if (provinceData) {
            app.selectedProvince = provinceData;
            updateSidebar(provinceData);
        } else {
            console.warn('No data found for province:', provinceName);
        }
    }

    function updateSidebar(province) {
        document.getElementById('no-selection').style.display = 'none';
        document.getElementById('province-details').style.display = 'block';

        document.getElementById('selected-province-name').textContent = province.name;
        document.getElementById('selected-province-population').textContent =
            (province.population ? province.population.toLocaleString() : 'N/A') + ' residents';

        // Format personnel count prominently
        const serviceCount = province.pestControlServices.length;
        const countText = serviceCount === 1 ? '1 Specialist' : `${serviceCount} Specialists`;
        document.getElementById('selected-service-count').textContent = countText;

        const list = document.getElementById('services-list');
        list.innerHTML = '';

        if (province.pestControlServices.length === 0) {
            list.innerHTML = '<div class="text-center py-8"><div class="text-gray-400 mb-2"><svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg></div><p class="text-gray-500 text-sm">No personnel available in this area yet</p></div>';
            return;
        }

        province.pestControlServices.forEach(service => {
            const card = document.createElement('div');
            card.className = 'service-card bg-white rounded-xl p-5 shadow-md hover:shadow-xl';
            card.innerHTML = `
                <div class="flex items-start justify-between mb-3">
                    <h5 class="font-bold text-gray-900 text-base">${service.name || 'Unknown Service'}</h5>
                    ${service.rating ? `
                        <div class="flex items-center bg-amber-50 px-2 py-1 rounded-lg">
                            <svg class="w-4 h-4 text-amber-500 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                            </svg>
                            <span class="font-bold text-amber-600 text-sm">${service.rating}</span>
                        </div>
                    ` : ''}
                </div>

                <p class="text-gray-600 text-sm mb-4 flex items-start">
                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>${service.address || 'Address not available'}</span>
                </p>

                <div class="mb-4">
                    <span class="inline-flex items-center bg-gradient-to-r from-purple-500 to-indigo-500 text-white text-xs px-3 py-1.5 rounded-full font-semibold shadow-sm">
                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        ${service.specialty || 'General Pest Control'}
                    </span>
                </div>

                <div class="space-y-2.5 text-sm border-t border-gray-100 pt-4">
                    ${service.phone ? `
                        <a href="tel:${service.phone}" class="flex items-center text-gray-700 hover:text-purple-600 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <span class="font-medium">${service.phone}</span>
                        </a>
                    ` : ''}
                    ${service.email ? `
                        <a href="mailto:${service.email}" class="flex items-center text-gray-700 hover:text-purple-600 transition-colors">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <span class="truncate font-medium">${service.email}</span>
                        </a>
                    ` : ''}
                    ${service.rating ? `
                        <div class="flex items-center text-gray-600 pt-2">
                            <span class="text-xs">${service.review_count || 0} customer reviews</span>
                        </div>
                    ` : ''}
                </div>

                <button class="mt-4 w-full bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg">
                    Contact Personnel
                </button>
            `;
            list.appendChild(card);
        });
    }

    function showTooltip(event, provinceName) {
        const tooltip = document.getElementById('province-tooltip');
        const provinceData = app.provinces[provinceName];

        if (provinceData) {
            const content = `
                <div class="font-bold text-gray-900 text-base mb-2">${provinceData.name}</div>
                <div class="text-sm text-gray-600 mb-1">
                    <span class="font-medium">${provinceData.population?.toLocaleString() || 'N/A'}</span> residents
                </div>
                <div class="text-sm text-purple-600 font-semibold mb-2">
                    ${provinceData.pestControlServices.length} personnel available
                </div>
                <div class="text-xs text-gray-500 italic">Click to view details</div>
            `;
            document.getElementById('tooltip-content').innerHTML = content;
            tooltip.classList.remove('hidden');
            moveTooltip(event);
        }
    }

    function moveTooltip(event) {
        const tooltip = document.getElementById('province-tooltip');
        tooltip.style.left = (event.pageX + 15) + 'px';
        tooltip.style.top = (event.pageY + 15) + 'px';
    }

    function hideTooltip() {
        document.getElementById('province-tooltip').classList.add('hidden');
    }

    // Handle window resize
    window.addEventListener('resize', () => {
        if (app.svg) {
            d3.select('#map-svg-container svg').remove();
            initMap();
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', initMap);
</script>
</body>
</html>
