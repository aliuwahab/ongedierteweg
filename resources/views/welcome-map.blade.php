<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netherlands Map - Pest Control Network</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script src="https://d3js.org/topojson.v3.min.js"></script>
    <style>
        .province {
            cursor: pointer;
            transition: all 0.3s ease;
            stroke: #fff;
            stroke-width: 1;
            stroke-linejoin: round;
        }

        .province:hover {
            opacity: 0.8;
            stroke-width: 2;
            stroke: #fbbf24;
        }

        .province.selected {
            stroke: #ef4444;
            stroke-width: 3;
        }

        .province-label {
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-weight: bold;
            fill: #1f2937;
            pointer-events: none;
            text-anchor: middle;
            text-shadow: 1px 1px 2px white, -1px -1px 2px white;
        }

        .service-card {
            transition: all 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        #map-container {
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
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
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-5xl font-bold text-gray-800 mb-4">Netherlands Pest Control Network</h1>
        <p class="text-gray-600 text-lg">Interactive Map - Click on any province to view pest control services</p>
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
                <div class="absolute bottom-6 left-6 bg-white bg-opacity-95 backdrop-blur p-4 rounded-xl shadow-lg">
                    <h3 class="font-bold text-gray-800 mb-3">Service Count</h3>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #10b981;"></div>
                        <span class="text-sm text-gray-700">1-2 services</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #3b82f6;"></div>
                        <span class="text-sm text-gray-700">3-4 services</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #8b5cf6;"></div>
                        <span class="text-sm text-gray-700">5+ services</span>
                    </div>
                </div>

                <!-- Province Info Tooltip -->
                <div id="province-tooltip" class="absolute hidden bg-gray-900 text-white px-4 py-3 rounded-lg shadow-xl z-50" style="pointer-events: none;">
                    <div id="tooltip-content"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl p-6 shadow-lg sticky top-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Province Details</h2>

                <div id="no-selection" class="text-gray-500 text-center py-8">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                    <p class="text-center">Click on a province on the map to view pest control services</p>
                </div>

                <div id="province-details" style="display: none;">
                    <div class="mb-6">
                        <h3 id="selected-province-name" class="text-2xl font-bold text-gray-800 mb-2"></h3>
                        <div class="flex items-center text-gray-600 text-sm mb-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span id="selected-province-population"></span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">
                            Pest Control Services (<span id="selected-service-count"></span>)
                        </h4>

                        <div id="services-list" class="space-y-3 max-h-96 overflow-y-auto"></div>
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
        path: null
    };

    console.log('Province data loaded:', app.provinces);

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
        document.getElementById('selected-service-count').textContent = province.pestControlServices.length;

        const list = document.getElementById('services-list');
        list.innerHTML = '';

        if (province.pestControlServices.length === 0) {
            list.innerHTML = '<div class="text-gray-500 text-center py-4">No pest control services available yet</div>';
            return;
        }

        province.pestControlServices.forEach(service => {
            const card = document.createElement('div');
            card.className = 'service-card bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 border border-gray-200';
            card.innerHTML = `
                <h5 class="font-bold text-gray-800 text-base mb-2">${service.name || 'Unknown Service'}</h5>
                <p class="text-gray-600 text-sm mb-3">${service.address || 'Address not available'}</p>

                <div class="flex items-center mb-3">
                    <span class="inline-flex items-center bg-gradient-to-r from-green-400 to-blue-500 text-white text-xs px-3 py-1 rounded-full font-medium">
                        ${service.specialty || 'General'}
                    </span>
                </div>

                <div class="space-y-2 text-sm">
                    ${service.phone ? `
                        <div class="flex items-center text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span>${service.phone}</span>
                        </div>
                    ` : ''}
                    ${service.email ? `
                        <div class="flex items-center text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="truncate">${service.email}</span>
                        </div>
                    ` : ''}
                    ${service.rating ? `
                        <div class="flex items-center text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                            </svg>
                            <span class="font-semibold text-yellow-600">${service.rating}</span>
                            <span class="ml-1 text-gray-600">(${service.review_count || 0} reviews)</span>
                        </div>
                    ` : ''}
                </div>
            `;
            list.appendChild(card);
        });
    }

    function showTooltip(event, provinceName) {
        const tooltip = document.getElementById('province-tooltip');
        const provinceData = app.provinces[provinceName];

        if (provinceData) {
            const content = `
                <div class="font-bold text-base mb-1">${provinceData.name}</div>
                <div class="text-sm">Population: ${provinceData.population?.toLocaleString() || 'N/A'}</div>
                <div class="text-sm">Services: ${provinceData.pestControlServices.length}</div>
                <div class="text-xs text-gray-400 mt-1">Click to view details</div>
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
