import * as THREE from 'three';

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('cosmic-background');
    if (!container) return;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });

    renderer.setSize(window.innerWidth, window.innerHeight);
    container.appendChild(renderer.domElement);

    // Stars
    const starsGeometry = new THREE.BufferGeometry();
    const starsMaterial = new THREE.PointsMaterial({ color: 0xffffff, size: 0.1, sizeAttenuation: true });

    const starsVertices = [];
    for (let i = 0; i < 10000; i++) {
        const x = (Math.random() - 0.5) * 2000;
        const y = (Math.random() - 0.5) * 2000;
        const z = -Math.random() * 2000;
        starsVertices.push(x, y, z);
    }

    starsGeometry.setAttribute('position', new THREE.Float32BufferAttribute(starsVertices, 3));
    const starField = new THREE.Points(starsGeometry, starsMaterial);
    scene.add(starField);

    // Spaceship creation
    const createSpaceship = () => {
        const shipGeometry = new THREE.ConeGeometry(0.5, 2, 4);
        shipGeometry.rotateX(Math.PI / 2);
        const shipMaterial = new THREE.ShaderMaterial({
            uniforms: {
                time: { value: 0 },
                baseColor: { value: new THREE.Color(0x8B5CF6) }
            },
            vertexShader: `
                varying vec2 vUv;
                void main() {
                    vUv = uv;
                    gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
                }
            `,
            fragmentShader: `
                uniform float time;
                uniform vec3 baseColor;
                varying vec2 vUv;
                
                void main() {
                    float pulse = sin(time * 5.0) * 0.1 + 0.9;
                    vec3 color = mix(baseColor, vec3(1.0), pulse);
                    gl_FragColor = vec4(color, 1.0);
                }
            `,
        });

        const ship = new THREE.Mesh(shipGeometry, shipMaterial);
        
        // Random starting position
        ship.position.set(
            (Math.random() - 0.5) * 100,
            (Math.random() - 0.5) * 100,
            -100
        );
        
        // Random rotation
        ship.rotation.z = Math.random() * Math.PI * 2;
        
        // Random speed
        ship.userData.speed = 0.2 + Math.random() * 0.3;
        
        return ship;
    };

    // Spaceships array
    const spaceships = [];
    const maxSpaceships = 3;

    // Function to add new spaceship
    const addSpaceship = () => {
        if (spaceships.length < maxSpaceships && Math.random() < 0.01) {
            const ship = createSpaceship();
            spaceships.push(ship);
            scene.add(ship);
        }
    };

    // Update spaceship positions
    const updateSpaceships = (time) => {
        for (let i = spaceships.length - 1; i >= 0; i--) {
            const ship = spaceships[i];
            
            // Move forward
            ship.position.z += ship.userData.speed;
            
            // Add slight wobble
            ship.position.x += Math.sin(time * 0.001 + i) * 0.01;
            ship.position.y += Math.cos(time * 0.001 + i) * 0.01;
            
            // Update material time uniform
            ship.material.uniforms.time.value = time * 0.001;
            
            // Remove if out of view
            if (ship.position.z > 10) {
                scene.remove(ship);
                spaceships.splice(i, 1);
            }
        }
    };

    // Black hole
    const blackHoleGeometry = new THREE.SphereGeometry(5, 32, 32);
    const blackHoleMaterial = new THREE.ShaderMaterial({
        uniforms: {
            time: { value: 0 },
        },
        vertexShader: `
            varying vec2 vUv;
            void main() {
                vUv = uv;
                gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
            }
        `,
        fragmentShader: `
            uniform float time;
            varying vec2 vUv;
            void main() {
                vec2 center = vec2(0.5, 0.5);
                float dist = distance(vUv, center);
                float alpha = smoothstep(0.5, 0.0, dist);
                vec3 color = mix(vec3(0.58, 0.2, 0.92), vec3(0.3, 0.1, 0.5), dist);
                float pulse = sin(time * 2.0) * 0.1 + 0.9;
                gl_FragColor = vec4(color, alpha * pulse);
            }
        `,
        transparent: true,
    });

    const blackHole = new THREE.Mesh(blackHoleGeometry, blackHoleMaterial);
    blackHole.position.set(0, 3, -10);
    scene.add(blackHole);

    camera.position.z = 5;

    const animate = (time) => {
        requestAnimationFrame(animate);
        starField.rotation.y += 0.0001;
        blackHoleMaterial.uniforms.time.value = time * 0.001;
        
        // Handle spaceships
        addSpaceship();
        updateSpaceships(time);
        
        renderer.render(scene, camera);
    };

    animate(0);

    const handleResize = () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    };

    window.addEventListener('resize', handleResize);

    // Cleanup on unload
    return () => {
        window.removeEventListener('resize', handleResize);
        container.removeChild(renderer.domElement);
    };
});
