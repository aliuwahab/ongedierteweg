<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netherlands 3D Map - Pest Control Network</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        #map-container {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            border-radius: 20px;
            position: relative;
            overflow: hidden;
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
        
        .service-card {
            transition: all 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        #map-canvas {
            cursor: grab;
        }
        
        #map-canvas:active {
            cursor: grabbing;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-800 min-h-screen">
<div x-data="simple3DMapApp()" x-init="init()" class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-5xl font-bold text-white mb-4">Netherlands Pest Control Network</h1>
        <p class="text-gray-300 text-lg">Interactive 3D Map - Click and drag to rotate</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- 3D Map Container -->
        <div class="lg:col-span-3">
            <div id="map-container" class="relative" style="height: 700px;">
                <canvas id="map-canvas"></canvas>
                
                <!-- Province Info Tooltip -->
                <div class="province-info" id="province-info">
                    <h4 class="font-bold text-lg mb-2" id="province-name"></h4>
                    <p class="text-sm mb-1" id="province-population"></p>
                    <p class="text-sm mb-3" id="province-services"></p>
                    <div class="text-xs text-gray-300">Click to view detailed services</div>
                </div>
                
                <!-- 3D Controls Info -->
                <div class="controls">
                    <h3 class="font-bold mb-2">Controls</h3>
                    <p class="text-xs mb-1">🖱️ Click + drag: Rotate</p>
                    <p class="text-xs mb-1">🖱️ Scroll: Zoom</p>
                    <p class="text-xs">📱 Touch: Drag to rotate</p>
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
    function simple3DMapApp() {
        return {
            selectedProvince: null,
            provinces: {},
            scene: null,
            camera: null,
            renderer: null,
            provinceObjects: [],
            raycaster: null,
            mouse: null,
            isMouseDown: false,
            mouseX: 0,
            mouseY: 0,
            rotationX: 0,
            rotationY: 0,

            init() {
                this.loadProvinceData();
                this.initThreeJS();
                this.createProvinces();
                this.setupEventListeners();
                this.animate();
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

                // Camera setup
                this.camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
                this.camera.position.set(0, 50, 80);
                this.camera.lookAt(0, 0, 0);

                // Renderer setup
                this.renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
                this.renderer.setSize(container.clientWidth, container.clientHeight);
                this.renderer.shadowMap.enabled = true;
                this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;

                // Lighting
                const ambientLight = new THREE.AmbientLight(0x404040, 0.6);
                this.scene.add(ambientLight);

                const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
                directionalLight.position.set(50, 50, 50);
                directionalLight.castShadow = true;
                this.scene.add(directionalLight);

                // Point lights for dramatic effect
                const pointLight1 = new THREE.PointLight(0x00ff88, 0.5, 100);
                pointLight1.position.set(-30, 20, 30);
                this.scene.add(pointLight1);

                const pointLight2 = new THREE.PointLight(0x0088ff, 0.5, 100);
                pointLight2.position.set(30, 20, -30);
                this.scene.add(pointLight2);

                // Raycaster for mouse picking
                this.raycaster = new THREE.Raycaster();
                this.mouse = new THREE.Vector2();

                // Create ground
                const groundGeometry = new THREE.PlaneGeometry(100, 100);
                const groundMaterial = new THREE.MeshLambertMaterial({ 
                    color: 0x111111,
                    transparent: true,
                    opacity: 0.3
                });
                const ground = new THREE.Mesh(groundGeometry, groundMaterial);
                ground.rotation.x = -Math.PI / 2;
                ground.receiveShadow = true;
                this.scene.add(ground);
            },

            createProvinces() {
                const provinceNames = Object.keys(this.provinces);
                const positions = this.calculateNetherlandsPositions();
                
                provinceNames.forEach((provinceName, index) => {
                    const province = this.provinces[provinceName];
                    const serviceCount = province.pestControlServices.length;
                    const position = positions[provinceName] || { x: (index % 4) * 15 - 22.5, z: Math.floor(index / 4) * 15 - 15 };
                    
                    // Create varied 3D shapes for each province
                    let geometry;
                    const shapeType = index % 4;
                    const height = 3 + (serviceCount * 1.5);
                    
                    switch(shapeType) {
                        case 0:
                            geometry = new THREE.BoxGeometry(8, height, 6);
                            break;
                        case 1:
                            geometry = new THREE.CylinderGeometry(4, 4, height, 8);
                            break;
                        case 2:
                            geometry = new THREE.ConeGeometry(4, height, 8);
                            break;
                        case 3:
                            geometry = new THREE.OctahedronGeometry(4);
                            geometry.scale(1, height/4, 1);
                            break;
                    }

                    // Color based on service count
                    const hue = (serviceCount / 5) * 0.6 + 0.1;
                    const color = new THREE.Color().setHSL(hue, 0.8, 0.6);
                    
                    const material = new THREE.MeshPhongMaterial({ 
                        color: color,
                        transparent: true,
                        opacity: 0.85,
                        shininess: 50
                    });

                    const provinceMesh = new THREE.Mesh(geometry, material);
                    provinceMesh.position.set(position.x, height/2, position.z);
                    provinceMesh.castShadow = true;
                    provinceMesh.receiveShadow = true;
                    
                    // Store province data
                    provinceMesh.userData = {
                        name: provinceName,
                        data: province,
                        originalColor: color.clone(),
                        originalOpacity: 0.85
                    };
                    
                    this.scene.add(provinceMesh);
                    this.provinceObjects.push(provinceMesh);

                    // Add province labels
                    this.createProvinceLabel(provinceName, position.x, height + 3, position.z);
                });
            },

            calculateNetherlandsPositions() {
                // Approximate positions of Dutch provinces
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

            setupEventListeners() {
                const canvas = document.getElementById('map-canvas');
                
                // Mouse events for rotation
                canvas.addEventListener('mousedown', (event) => {
                    this.isMouseDown = true;
                    this.mouseX = event.clientX;
                    this.mouseY = event.clientY;
                });

                canvas.addEventListener('mousemove', (event) => {
                    if (this.isMouseDown) {
                        const deltaX = event.clientX - this.mouseX;
                        const deltaY = event.clientY - this.mouseY;
                        
                        this.rotationY += deltaX * 0.01;
                        this.rotationX += deltaY * 0.01;
                        
                        // Limit vertical rotation
                        this.rotationX = Math.max(-Math.PI/3, Math.min(Math.PI/3, this.rotationX));
                        
                        this.mouseX = event.clientX;
                        this.mouseY = event.clientY;
                    } else {
                        // Handle hover for province highlighting
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
                            hoveredObject.scale.set(1.1, 1.1, 1.1);
                            canvas.style.cursor = 'pointer';
                            
                            this.showProvinceInfo(hoveredObject, event);
                        } else {
                            canvas.style.cursor = this.isMouseDown ? 'grabbing' : 'grab';
                            this.hideProvinceInfo();
                        }
                    }
                });

                canvas.addEventListener('mouseup', () => {
                    this.isMouseDown = false;
                    canvas.style.cursor = 'grab';
                });

                // Click events for selection
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

                // Zoom with mouse wheel
                canvas.addEventListener('wheel', (event) => {
                    event.preventDefault();
                    const zoomSpeed = 0.1;
                    if (event.deltaY > 0) {
                        this.camera.position.multiplyScalar(1 + zoomSpeed);
                    } else {
                        this.camera.position.multiplyScalar(1 - zoomSpeed);
                    }
                    // Keep camera distance reasonable
                    const distance = this.camera.position.length();
                    if (distance < 30) this.camera.position.normalize().multiplyScalar(30);
                    if (distance > 150) this.camera.position.normalize().multiplyScalar(150);
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
                provinceObject.scale.set(1.2, 1.2, 1.2);
                
                // Update selected province data
                this.selectedProvince = provinceObject.userData.data;
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
                
                // Apply manual rotation
                const group = new THREE.Object3D();
                this.provinceObjects.forEach(obj => {
                    group.add(obj);
                });
                
                // Update camera position based on rotation
                const radius = this.camera.position.length();
                this.camera.position.x = radius * Math.sin(this.rotationY) * Math.cos(this.rotationX);
                this.camera.position.y = radius * Math.sin(this.rotationX);
                this.camera.position.z = radius * Math.cos(this.rotationY) * Math.cos(this.rotationX);
                this.camera.lookAt(0, 0, 0);
                
                this.renderer.render(this.scene, this.camera);
            }
        }
    }
</script>
</body>
</html>