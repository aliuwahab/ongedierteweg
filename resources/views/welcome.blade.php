<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netherlands Pest Control Map</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script src="https://unpkg.com/topojson@3"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .province {
            stroke: #2c3e50;
            stroke-width: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .province:hover {
            filter: brightness(0.9);
            transform: translateY(-2px);
        }

        .province.active {
            filter: brightness(0.8);
            stroke-width: 2px;
        }

        .map-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .tooltip {
            position: absolute;
            padding: 10px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            border-radius: 5px;
            pointer-events: none;
            font-size: 14px;
            z-index: 1000;
        }

        /* 3D effect styling */
        svg {
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 min-h-screen">
<div x-data="mapApp()" x-init="initMap()" class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-white mb-2">Netherlands Pest Control Network</h1>
        <p class="text-gray-300">Click on any province to view local pest control professionals</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Map Container -->
        <div class="lg:col-span-3">
            <div class="map-container p-8 bg-white bg-opacity-10 backdrop-blur-md">
                <div id="map" class="w-full" style="height: 600px;"></div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-6 sticky top-8">
                <h2 class="text-2xl font-bold text-white mb-4">Province Details</h2>

                <template x-if="!selectedProvince">
                    <div class="text-gray-300">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                        <p class="text-center">Select a province to view pest control services</p>
                    </div>
                </template>

                <template x-if="selectedProvince">
                    <div>
                        <h3 class="text-xl font-semibold text-white mb-4" x-text="selectedProvince.name"></h3>

                        <div class="space-y-4">
                            <template x-for="pestman in selectedProvince.pestControlServices" :key="pestman.id">
                                <div class="bg-white bg-opacity-20 rounded-lg p-4 hover:bg-opacity-30 transition-all">
                                    <h4 class="font-bold text-white" x-text="pestman.name"></h4>
                                    <p class="text-gray-300 text-sm mt-1" x-text="pestman.address"></p>
                                    <div class="mt-2">
                                        <span class="inline-block bg-green-500 text-white text-xs px-2 py-1 rounded-full" x-text="pestman.specialty"></span>
                                    </div>
                                    <p class="text-gray-300 text-sm mt-2">
                                        <span class="font-semibold">Phone:</span> <span x-text="pestman.phone"></span>
                                    </p>
                                    <p class="text-gray-300 text-sm">
                                        <span class="font-semibold">Rating:</span>
                                        <span class="text-yellow-400" x-text="'★'.repeat(Math.floor(pestman.rating))"></span>
                                        <span x-text="`(${pestman.rating})`"></span>
                                    </p>
                                </div>
                            </template>
                        </div>

                        <div class="mt-6 pt-6 border-t border-white border-opacity-20">
                            <p class="text-gray-300 text-sm">
                                <span class="font-semibold">Total Services:</span>
                                <span x-text="selectedProvince.pestControlServices.length"></span>
                            </p>
                            <p class="text-gray-300 text-sm mt-1">
                                <span class="font-semibold">Population:</span>
                                <span x-text="selectedProvince.population?.toLocaleString() || 'N/A'"></span>
                            </p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<div class="tooltip" style="display: none;"></div>

<script>
    function mapApp() {
        return {
            selectedProvince: null,
            provinces: {},

            initMap() {
                this.loadProvinceData();
                this.createMap();
            },

            loadProvinceData() {
                // Load data from Laravel backend
                this.provinces = @json($provinces);
            },

            createMap() {
                const width = document.getElementById('map').clientWidth;
                const height = 600;

                const svg = d3.select("#map")
                    .append("svg")
                    .attr("width", width)
                    .attr("height", height);

                const projection = d3.geoMercator()
                    .center([5.5, 52.2])
                    .scale(6000)
                    .translate([width / 2, height / 2]);

                const path = d3.geoPath().projection(projection);

                const tooltip = d3.select(".tooltip");

                // Color scale for provinces
                const colorScale = d3.scaleSequential(d3.interpolateBlues)
                    .domain([0, 12]);

                // Create improved grid layout with better styling
                const fallbackProvinces = Object.keys(this.provinces);
                const cols = 4;
                const rows = Math.ceil(fallbackProvinces.length / cols);
                const rectWidth = (width - 60) / cols;
                const rectHeight = (height - 60) / rows;

                // Create province cards with 3D effect
                const provinceGroups = svg.selectAll(".province-group")
                    .data(fallbackProvinces)
                    .enter()
                    .append("g")
                    .attr("class", "province-group");

                // Add drop shadow filter
                const defs = svg.append("defs");
                const filter = defs.append("filter")
                    .attr("id", "drop-shadow")
                    .attr("height", "130%");
                filter.append("feGaussianBlur")
                    .attr("in", "SourceAlpha")
                    .attr("stdDeviation", 3);
                filter.append("feOffset")
                    .attr("dx", 2)
                    .attr("dy", 2)
                    .attr("result", "offset");
                const feMerge = filter.append("feMerge");
                feMerge.append("feMergeNode")
                    .attr("in", "offset");
                feMerge.append("feMergeNode")
                    .attr("in", "SourceGraphic");

                // Create the main province rectangles
                provinceGroups
                    .append("rect")
                    .attr("class", "province")
                    .attr("x", (d, i) => 30 + (i % cols) * rectWidth)
                    .attr("y", (d, i) => 30 + Math.floor(i / cols) * rectHeight)
                    .attr("width", rectWidth - 15)
                    .attr("height", rectHeight - 15)
                    .attr("rx", 15)
                    .attr("ry", 15)
                    .style("fill", (d, i) => {
                        const province = this.provinces[d];
                        const serviceCount = province.pestControlServices.length;
                        return d3.interpolateViridis(serviceCount / 5); // Scale based on service count
                    })
                    .style("opacity", 0.85)
                    .style("filter", "url(#drop-shadow)")
                    .style("stroke", "#ffffff")
                    .style("stroke-width", "2px")
                    .on("click", (event, provinceName) => {
                        svg.selectAll(".province").classed("active", false);
                        d3.select(event.target).classed("active", true);
                        this.selectedProvince = this.provinces[provinceName];
                    })
                    .on("mouseover", function(event, d) {
                        d3.select(this)
                            .style("opacity", 1.0)
                            .style("stroke-width", "3px")
                            .style("stroke", "#ffcc00");
                        
                        const province = this.provinces[d];
                        tooltip.style("display", "block")
                            .html(`
                                <strong>${d}</strong><br/>
                                Population: ${province.population?.toLocaleString()}<br/>
                                Services: ${province.pestControlServices.length}
                            `)
                            .style("left", (event.pageX + 10) + "px")
                            .style("top", (event.pageY - 10) + "px");
                    }.bind(this))
                    .on("mouseout", function() {
                        d3.select(this)
                            .style("opacity", 0.85)
                            .style("stroke-width", "2px")
                            .style("stroke", "#ffffff");
                        tooltip.style("display", "none");
                    });

                // Add province labels with better styling
                provinceGroups
                    .append("text")
                    .attr("class", "province-label")
                    .attr("x", (d, i) => 30 + (i % cols) * rectWidth + (rectWidth - 15)/2)
                    .attr("y", (d, i) => 30 + Math.floor(i / cols) * rectHeight + (rectHeight - 15)/2 - 10)
                    .style("text-anchor", "middle")
                    .style("font-size", "14px")
                    .style("fill", "white")
                    .style("font-weight", "bold")
                    .style("pointer-events", "none")
                    .style("text-shadow", "2px 2px 4px rgba(0,0,0,0.8)")
                    .text(d => d);

                // Add service count indicators
                provinceGroups
                    .append("text")
                    .attr("class", "service-count")
                    .attr("x", (d, i) => 30 + (i % cols) * rectWidth + (rectWidth - 15)/2)
                    .attr("y", (d, i) => 30 + Math.floor(i / cols) * rectHeight + (rectHeight - 15)/2 + 15)
                    .style("text-anchor", "middle")
                    .style("font-size", "12px")
                    .style("fill", "#cccccc")
                    .style("pointer-events", "none")
                    .style("text-shadow", "1px 1px 2px rgba(0,0,0,0.8)")
                    .text(d => {
                        const count = this.provinces[d].pestControlServices.length;
                        return count === 1 ? '1 service' : `${count} services`;
                    });

                // Add subtle animation
                provinceGroups.selectAll(".province")
                    .style("transform-origin", "center")
                    .on("mouseenter", function() {
                        d3.select(this)
                            .transition()
                            .duration(200)
                            .style("transform", "scale(1.05)");
                    })
                    .on("mouseleave", function() {
                        d3.select(this)
                            .transition()
                            .duration(200)
                            .style("transform", "scale(1)");
                    });
            }
        }
    }
</script>
</body>
</html>
