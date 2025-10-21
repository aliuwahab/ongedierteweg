<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netherlands Geographic 3D Map - Pest Control Network</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.134.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <script src="https://unpkg.com/topojson@3"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        #map-container {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            border-radius: 20px;
            position: relative;
            overflow: hidden;
        }
        
        #map-canvas {
            border-radius: 20px;
        }
        
        .province-info {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 15px;
            border-radius: 10px;
            display: none;
            z-index: 1000;
            max-width: 250px;
            backdrop-filter: blur(10px);
        }
        
        .controls {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 15px;
            border-radius: 10px;
            z-index: 1000;
        }
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            z-index: 2000;
        }
        
        .service-card {
            transition: all 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 min-h-screen">
<div x-data="geographicMapApp()" x-init="init()" class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-5xl font-bold text-white mb-4">Netherlands Pest Control Network</h1>
        <p class="text-gray-300 text-lg">Interactive 3D Geographic Map of the Netherlands</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- 3D Geographic Map Container -->
        <div class="lg:col-span-3">
            <div id="map-container" class="relative" style="height: 700px;">
                <canvas id="map-canvas"></canvas>
                
                <!-- Loading Overlay -->
                <div class="loading-overlay" id="loading-overlay">
                    <div class="text-center">
                        <div class="spinner mx-auto mb-4"></div>
                        <p>Loading Netherlands geographic data...</p>
                    </div>
                </div>
                
                <!-- Province Info Tooltip -->
                <div class="province-info" id="province-info">
                    <h4 class="font-bold text-lg mb-2" id="province-name"></h4>
                    <p class="text-sm mb-1" id="province-population"></p>
                    <p class="text-sm mb-3" id="province-services"></p>
                    <div class="text-xs text-gray-300">Click to view detailed services</div>
                </div>
                
                <!-- 3D Controls Info -->
                <div class="controls">
                    <h3 class="font-bold mb-2">3D Controls</h3>
                    <p class="text-xs mb-1">🖱️ Left click + drag: Rotate</p>
                    <p class="text-xs mb-1">🖱️ Right click + drag: Pan</p>
                    <p class="text-xs mb-1">🖱️ Scroll: Zoom</p>
                    <p class="text-xs">📱 Touch: Pinch & drag</p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-xl p-6 sticky top-8">
                <h2 class="text-2xl font-bold text-white mb-6">Province Details</h2>

                <template x-if="!selectedProvince">
                    <div class="text-gray-300 text-center py-8">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                        </div>
                        <p class="text-center">Click on a province in the 3D map to view pest control services</p>
                    </div>
                </template>

                <template x-if="selectedProvince">
                    <div>
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-white mb-2" x-text="selectedProvince.name"></h3>
                            <div class="flex items-center text-gray-300 text-sm mb-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span x-text="selectedProvince.population?.toLocaleString() + ' residents' || 'N/A'"></span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h4 class="text-lg font-semibold text-white border-b border-gray-600 pb-2">
                                Pest Control Services (<span x-text="selectedProvince.pestControlServices.length"></span>)
                            </h4>
                            
                            <template x-for="service in selectedProvince.pestControlServices" :key="service.id">
                                <div class="service-card bg-white bg-opacity-15 rounded-xl p-4 hover:bg-opacity-25 transition-all">
                                    <h5 class="font-bold text-white text-lg" x-text="service.name"></h5>
                                    <p class="text-gray-300 text-sm mt-1" x-text="service.address"></p>
                                    
                                    <div class="flex items-center mt-3">
                                        <span class="inline-flex items-center bg-gradient-to-r from-green-400 to-blue-500 text-white text-xs px-3 py-1 rounded-full font-medium" x-text="service.specialty"></span>
                                    </div>
                                    
                                    <div class="mt-3 space-y-1">
                                        <p class="text-gray-300 text-sm flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                            <span x-text="service.phone"></span>
                                        </p>
                                        <p class="text-gray-300 text-sm flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                            </svg>
                                            <span class="text-yellow-400 font-semibold" x-text="service.rating"></span>
                                            <span class="ml-1" x-text="'(' + (service.review_count || 0) + ' reviews)'"></span>
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
    function geographicMapApp() {
        return {
            selectedProvince: null,
            provinces: {},
            scene: null,
            camera: null,
            renderer: null,
            controls: null,
            provinceObjects: [],
            raycaster: null,
            mouse: null,
            geoData: null,

            init() {
                this.loadProvinceData();
                this.initThreeJS();
                this.loadGeoData();
            },

            loadProvinceData() {
                // Load data from Laravel backend
                this.provinces = @json($provinces);
            },

            initThreeJS() {
                const container = document.getElementById('map-container');
                const canvas = document.getElementById('map-canvas');
                
                // Scene setup
                this.scene = new THREE.Scene();
                this.scene.background = new THREE.Color(0x0a0a0a);

                // Camera setup - positioned to view Netherlands from above
                this.camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
                this.camera.position.set(0, 80, 100);
                this.camera.lookAt(0, 0, 0);

                // Renderer setup
                this.renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
                this.renderer.setSize(container.clientWidth, container.clientHeight);
                this.renderer.shadowMap.enabled = true;
                this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;

                // Controls - wait for OrbitControls to be available
                if (typeof THREE.OrbitControls !== 'undefined') {
                    this.controls = new THREE.OrbitControls(this.camera, canvas);
                } else {
                    // Fallback if OrbitControls not loaded
                    setTimeout(() => {
                        if (typeof THREE.OrbitControls !== 'undefined') {
                            this.controls = new THREE.OrbitControls(this.camera, canvas);
                            this.setupControls();
                        }
                    }, 100);
                    return;
                }
                this.setupControls();

                // Advanced Lighting Setup
                const ambientLight = new THREE.AmbientLight(0x404040, 0.4);
                this.scene.add(ambientLight);

                const directionalLight = new THREE.DirectionalLight(0xffffff, 1.0);
                directionalLight.position.set(50, 100, 50);
                directionalLight.castShadow = true;
                directionalLight.shadow.mapSize.width = 4096;
                directionalLight.shadow.mapSize.height = 4096;
                directionalLight.shadow.camera.near = 0.1;
                directionalLight.shadow.camera.far = 500;
                directionalLight.shadow.camera.left = -100;
                directionalLight.shadow.camera.right = 100;
                directionalLight.shadow.camera.top = 100;
                directionalLight.shadow.camera.bottom = -100;
                this.scene.add(directionalLight);

                // Accent lights for dramatic effect
                const pointLight1 = new THREE.PointLight(0x00aaff, 0.6, 100);
                pointLight1.position.set(-50, 30, 30);
                this.scene.add(pointLight1);

                const pointLight2 = new THREE.PointLight(0xff6600, 0.6, 100);
                pointLight2.position.set(50, 30, -30);
                this.scene.add(pointLight2);

                // Raycaster for mouse picking
                this.raycaster = new THREE.Raycaster();
                this.mouse = new THREE.Vector2();

                this.animate();
                this.setupEventListeners();
            },

            setupControls() {
                if (!this.controls) return;
                this.controls.enableDamping = true;
                this.controls.dampingFactor = 0.05;
                this.controls.maxPolarAngle = Math.PI / 2.2;
                this.controls.minDistance = 50;
                this.controls.maxDistance = 200;
            },

            async loadGeoData() {
                try {
                    // Load Netherlands GeoJSON data
                    const response = await fetch('https://raw.githubusercontent.com/holtzy/D3-graph-gallery/master/DATA/world.geojson');
                    const worldData = await response.json();
                    
                    // Filter for Netherlands
                    const netherlandsFeatures = worldData.features.filter(feature => 
                        feature.properties.NAME === 'Netherlands' || 
                        feature.properties.name === 'Netherlands' ||
                        feature.properties.NAME_EN === 'Netherlands'
                    );

                    if (netherlandsFeatures.length === 0) {
                        // Fallback: Load provinces data
                        const provincesResponse = await fetch('https://raw.githubusercontent.com/cartomap/nl/master/data/simplify-0.0005/netherlands-provinces.geojson');
                        this.geoData = await provincesResponse.json();
                    } else {
                        // Create provinces from Netherlands data
                        this.geoData = {
                            type: "FeatureCollection",
                            features: netherlandsFeatures
                        };
                    }

                    document.getElementById('loading-overlay').style.display = 'none';
                    this.createGeographicMap();
                } catch (error) {
                    console.error('Error loading geographic data:', error);
                    document.getElementById('loading-overlay').style.display = 'none';
                    this.createFallbackMap();
                }
            },

            createGeographicMap() {
                if (!this.geoData) return;

                // Set up projection for Netherlands
                const projection = d3.geoMercator()
                    .fitSize([200, 200], this.geoData)
                    .translate([0, 0]);

                const geoPath = d3.geoPath().projection(projection);

                this.geoData.features.forEach((feature, index) => {
                    // Get province name (try different property names)
                    const provinceName = feature.properties.name || 
                                       feature.properties.NAME || 
                                       feature.properties.province || 
                                       `Province ${index + 1}`;

                    // Create 3D geometry from GeoJSON
                    const coordinates = feature.geometry.coordinates;
                    const shape = this.createShapeFromCoordinates(coordinates, projection);
                    
                    if (shape) {
                        const serviceCount = this.provinces[provinceName]?.pestControlServices?.length || 1;
                        const extrudeHeight = 2 + (serviceCount * 0.8);

                        // Extrude the shape to create 3D province
                        const geometry = new THREE.ExtrudeGeometry(shape, {
                            depth: extrudeHeight,
                            bevelEnabled: true,
                            bevelThickness: 0.5,
                            bevelSize: 0.3,
                            bevelOffset: 0,
                            bevelSegments: 3
                        });

                        // Create materials based on service count
                        const hue = (serviceCount - 1) / 5; // Normalize to 0-1
                        const color = new THREE.Color().setHSL(hue * 0.6 + 0.1, 0.8, 0.6);
                        
                        const material = new THREE.MeshPhongMaterial({ 
                            color: color,
                            transparent: true,
                            opacity: 0.85,
                            shininess: 30
                        });

                        const provinceMesh = new THREE.Mesh(geometry, material);
                        provinceMesh.castShadow = true;
                        provinceMesh.receiveShadow = true;
                        
                        // Store province data
                        provinceMesh.userData = {
                            name: provinceName,
                            data: this.provinces[provinceName] || this.createDefaultProvinceData(provinceName),
                            originalColor: color.clone(),
                            originalOpacity: 0.85
                        };
                        
                        this.scene.add(provinceMesh);
                        this.provinceObjects.push(provinceMesh);
                    }
                });

                // If no provinces were created, fall back to grid
                if (this.provinceObjects.length === 0) {
                    this.createFallbackMap();
                    return;
                }

                this.createWaterAndGround();
            },

            createShapeFromCoordinates(coordinates, projection) {
                try {
                    const shape = new THREE.Shape();
                    let isFirstPoint = true;

                    // Handle different coordinate structures
                    const processCoordinates = (coords) => {
                        if (coords.length === 0) return;

                        coords.forEach((coord, i) => {
                            if (Array.isArray(coord[0])) {
                                // Multi-polygon or nested array
                                processCoordinates(coord);
                            } else {
                                // Single coordinate pair
                                const [lon, lat] = coord;
                                const [x, y] = projection([lon, lat]) || [0, 0];
                                
                                if (isFirstPoint) {
                                    shape.moveTo(x, -y); // Flip Y for Three.js
                                    isFirstPoint = false;
                                } else {
                                    shape.lineTo(x, -y);
                                }
                            }
                        });
                    };

                    if (coordinates[0] && Array.isArray(coordinates[0])) {
                        processCoordinates(coordinates[0]); // Take first polygon
                    } else {
                        processCoordinates(coordinates);
                    }

                    return shape.getPoints().length > 2 ? shape : null;
                } catch (error) {
                    console.error('Error creating shape:', error);
                    return null;
                }
            },

            createFallbackMap() {
                // Create stylized province representations when geo data fails
                const provinceNames = Object.keys(this.provinces);
                const positions = this.calculateNetherlandsPositions();
                
                provinceNames.forEach((provinceName, index) => {
                    const province = this.provinces[provinceName];
                    const serviceCount = province.pestControlServices.length;
                    const position = positions[provinceName] || { x: (index % 4) * 15 - 22.5, z: Math.floor(index / 4) * 15 - 15 };
                    
                    // Create varied shapes for each province
                    const shapes = this.getProvinceShapes();
                    const shapeType = index % shapes.length;
                    const geometry = shapes[shapeType];
                    
                    // Scale based on service count
                    const scale = 1 + (serviceCount * 0.2);
                    geometry.scale(scale, 1 + serviceCount * 0.5, scale);

                    const hue = (serviceCount / 5) * 0.6 + 0.1;
                    const color = new THREE.Color().setHSL(hue, 0.8, 0.6);
                    
                    const material = new THREE.MeshPhongMaterial({ 
                        color: color,
                        transparent: true,
                        opacity: 0.85,
                        shininess: 50
                    });

                    const provinceMesh = new THREE.Mesh(geometry, material);
                    provinceMesh.position.set(position.x, 2, position.z);
                    provinceMesh.castShadow = true;
                    provinceMesh.receiveShadow = true;
                    
                    provinceMesh.userData = {
                        name: provinceName,
                        data: province,
                        originalColor: color.clone(),
                        originalOpacity: 0.85
                    };
                    
                    this.scene.add(provinceMesh);
                    this.provinceObjects.push(provinceMesh);

                    // Add province labels
                    this.createProvinceLabel(provinceName, position.x, 6 + serviceCount, position.z);
                });

                this.createWaterAndGround();
            },

            getProvinceShapes() {
                return [
                    new THREE.BoxGeometry(8, 4, 6),
                    new THREE.CylinderGeometry(4, 4, 4, 6),
                    new THREE.ConeGeometry(4, 6, 8),
                    new THREE.TetrahedronGeometry(4),
                ];
            },

            calculateNetherlandsPositions() {
                // Approximate positions of Dutch provinces for fallback
                return {
                    'Noord-Holland': { x: -5, z: 15 },
                    'Zuid-Holland': { x: -10, z: 5 },
                    'Utrecht': { x: -5, z: 5 },
                    'Zeeland': { x: -15, z: -5 },
                    'Noord-Brabant': { x: -5, z: -10 },
                    'Limburg': { x: 5, z: -15 },
                    'Gelderland': { x: 5, z: 0 },
                    'Overijssel': { x: 10, z: 10 },
                    'Drenthe': { x: 15, z: 15 },
                    'Groningen': { x: 20, z: 20 },
                    'Friesland': { x: 10, z: 20 },
                    'Flevoland': { x: 0, z: 10 }
                };
            },

            createWaterAndGround() {
                // Create water surface (North Sea)
                const waterGeometry = new THREE.PlaneGeometry(150, 150);
                const waterMaterial = new THREE.MeshPhongMaterial({ 
                    color: 0x006994,
                    transparent: true,
                    opacity: 0.6,
                    shininess: 100
                });
                const water = new THREE.Mesh(waterGeometry, waterMaterial);
                water.rotation.x = -Math.PI / 2;
                water.position.y = -0.5;
                water.receiveShadow = true;
                this.scene.add(water);

                // Create ground/base
                const groundGeometry = new THREE.PlaneGeometry(120, 120);
                const groundMaterial = new THREE.MeshLambertMaterial({ 
                    color: 0x2d5016,
                    transparent: true,
                    opacity: 0.4
                });
                const ground = new THREE.Mesh(groundGeometry, groundMaterial);
                ground.rotation.x = -Math.PI / 2;
                ground.position.y = -1;
                ground.receiveShadow = true;
                this.scene.add(ground);
            },

            createProvinceLabel(text, x, y, z) {
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.width = 256;
                canvas.height = 64;
                
                context.fillStyle = 'rgba(0, 0, 0, 0.8)';
                context.fillRect(0, 0, canvas.width, canvas.height);
                context.fillStyle = 'white';
                context.font = 'bold 18px Arial';
                context.textAlign = 'center';
                context.fillText(text, canvas.width/2, canvas.height/2 + 6);
                
                const texture = new THREE.CanvasTexture(canvas);
                const material = new THREE.SpriteMaterial({ map: texture });
                const sprite = new THREE.Sprite(material);
                sprite.position.set(x, y, z);
                sprite.scale.set(12, 3, 1);
                this.scene.add(sprite);
            },

            createDefaultProvinceData(name) {
                return {
                    name: name,
                    population: Math.floor(Math.random() * 1000000) + 100000,
                    pestControlServices: [
                        {
                            id: Math.random(),
                            name: `${name} Pest Control Services`,
                            address: `Main Street, ${name}`,
                            phone: `+31 ${Math.floor(Math.random() * 90) + 10} ${Math.floor(Math.random() * 900) + 100} ${Math.floor(Math.random() * 9000) + 1000}`,
                            specialty: 'General Pest Control',
                            rating: (Math.random() * 2 + 3).toFixed(1),
                            review_count: Math.floor(Math.random() * 50) + 10
                        }
                    ]
                };
            },

            setupEventListeners() {
                const canvas = document.getElementById('map-canvas');
                
                canvas.addEventListener('click', (event) => {
                    const rect = canvas.getBoundingClientRect();
                    this.mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
                    this.mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
                    
                    this.raycaster.setFromCamera(this.mouse, this.camera);
                    const intersects = this.raycaster.intersectObjects(this.provinceObjects);
                    
                    if (intersects.length > 0) {
                        this.selectProvince(intersects[0].object);
                    }
                });
                
                canvas.addEventListener('mousemove', (event) => {
                    const rect = canvas.getBoundingClientRect();
                    this.mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
                    this.mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
                    
                    this.raycaster.setFromCamera(this.mouse, this.camera);
                    const intersects = this.raycaster.intersectObjects(this.provinceObjects);
                    
                    // Reset all provinces
                    this.provinceObjects.forEach(obj => {
                        obj.material.color.copy(obj.userData.originalColor);
                        obj.material.opacity = obj.userData.originalOpacity;
                        obj.scale.set(1, 1, 1);
                    });
                    
                    // Highlight hovered province
                    if (intersects.length > 0) {
                        const hoveredObject = intersects[0].object;
                        hoveredObject.material.color.setHex(0xffaa00);
                        hoveredObject.material.opacity = 1.0;
                        hoveredObject.scale.set(1.05, 1.1, 1.05);
                        canvas.style.cursor = 'pointer';
                        
                        this.showProvinceInfo(hoveredObject, event);
                    } else {
                        canvas.style.cursor = 'default';
                        this.hideProvinceInfo();
                    }
                });

                // Handle window resize
                window.addEventListener('resize', () => {
                    const container = document.getElementById('map-container');
                    this.camera.aspect = container.clientWidth / container.clientHeight;
                    this.camera.updateProjectionMatrix();
                    this.renderer.setSize(container.clientWidth, container.clientHeight);
                });
            },

            selectProvince(provinceObject) {
                // Reset all provinces
                this.provinceObjects.forEach(obj => {
                    obj.material.color.copy(obj.userData.originalColor);
                    obj.material.opacity = obj.userData.originalOpacity;
                    obj.scale.set(1, 1, 1);
                });
                
                // Highlight selected province
                provinceObject.material.color.setHex(0xff3300);
                provinceObject.material.opacity = 1.0;
                provinceObject.scale.set(1.1, 1.2, 1.1);
                
                // Update selected province data
                this.selectedProvince = provinceObject.userData.data;
                
                // Smooth camera movement to province
                this.animateCamera(provinceObject.position);
            },

            animateCamera(targetPosition) {
                const startPosition = this.camera.position.clone();
                const endPosition = new THREE.Vector3(
                    targetPosition.x + 20,
                    targetPosition.y + 30,
                    targetPosition.z + 20
                );
                
                let progress = 0;
                const duration = 1500; // 1.5 seconds
                const startTime = Date.now();
                
                const animate = () => {
                    const elapsed = Date.now() - startTime;
                    progress = Math.min(elapsed / duration, 1);
                    
                    // Smooth easing function
                    const eased = 1 - Math.pow(1 - progress, 3);
                    
                    this.camera.position.lerpVectors(startPosition, endPosition, eased);
                    if (this.controls) {
                        this.controls.target.lerp(targetPosition, eased);
                        this.controls.update();
                    }
                    
                    if (progress < 1) {
                        requestAnimationFrame(animate);
                    }
                };
                
                animate();
            },

            showProvinceInfo(provinceObject, event) {
                const infoDiv = document.getElementById('province-info');
                const data = provinceObject.userData.data;
                
                document.getElementById('province-name').textContent = data.name;
                document.getElementById('province-population').textContent = 
                    `Population: ${data.population?.toLocaleString() || 'N/A'}`;
                document.getElementById('province-services').textContent = 
                    `Pest Control Services: ${data.pestControlServices.length}`;
                
                infoDiv.style.display = 'block';
                infoDiv.style.left = Math.min(event.clientX + 10, window.innerWidth - 270) + 'px';
                infoDiv.style.top = Math.max(event.clientY - 100, 10) + 'px';
            },

            hideProvinceInfo() {
                document.getElementById('province-info').style.display = 'none';
            },

            animate() {
                requestAnimationFrame(() => this.animate());
                
                if (this.controls) {
                    this.controls.update();
                }
                
                // Add subtle movement to water
                const time = Date.now() * 0.001;
                this.scene.children.forEach(child => {
                    if (child.material && child.material.color && child.material.color.getHex() === 0x006994) {
                        child.position.y = -0.5 + Math.sin(time) * 0.2;
                    }
                });
                
                this.renderer.render(this.scene, this.camera);
            }
        }
    }
</script>
</body>
</html>