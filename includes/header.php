<?php
require_once __DIR__ . '/../config/config.php';
$currentUser = getCurrentUser();
$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Campus Navigator - Find buildings, services, and routes across the university">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Campus Navigator</title>
    <link rel="stylesheet" href="css/index.css?v=1">
    <?php if (isset($customCSS) && !empty($customCSS)): ?>
        <link rel="stylesheet" href="css/<?php echo htmlspecialchars($customCSS); ?>">
    <?php endif; ?>
    <!-- Map loader and Web Speech API for Voice Navigation -->
    <script>
        // Load Google Maps dynamically when needed. Returns a Promise.
        function loadGoogleMaps() {
            return new Promise((resolve, reject) => {
                if (window.google && window.google.maps) {
                    return resolve(window.google.maps);
                }

                const apiKey = '<?php echo GOOGLE_MAPS_API_KEY; ?>';
                if (!apiKey) {
                    return reject(new Error('Google Maps API key not configured'));
                }

                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places,geometry`;
                script.async = true;
                script.defer = true;

                const timer = setTimeout(() => {
                    script.onerror = null;
                    reject(new Error('Google Maps load timeout'));
                }, 10000);

                script.onload = () => {
                    clearTimeout(timer);
                    if (window.google && window.google.maps) {
                        resolve(window.google.maps);
                    } else {
                        reject(new Error('Google Maps loaded but unavailable'));
                    }
                };

                script.onerror = (e) => {
                    clearTimeout(timer);
                    reject(new Error('Failed to load Google Maps'));
                };

                document.head.appendChild(script);
            });
        }

        // Simple SVG fallback to display when Google Maps is unavailable
        function initSimpleMap(containerId, options = {}) {
            const el = document.getElementById(containerId);
            if (!el) return;
            el.innerHTML = '';
            el.style.background = '#e8f0e8';
            el.style.display = 'flex';
            el.style.alignItems = 'center';
            el.style.justifyContent = 'center';

            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('width', '100%');
            svg.setAttribute('height', '100%');
            svg.setAttribute('viewBox', '0 0 600 400');

            const rect = document.createElementNS(svg.namespaceURI, 'rect');
            rect.setAttribute('width', '100%');
            rect.setAttribute('height', '100%');
            rect.setAttribute('fill', '#e8f0e8');
            svg.appendChild(rect);

            const text = document.createElementNS(svg.namespaceURI, 'text');
            text.setAttribute('x', '50%');
            text.setAttribute('y', '50%');
            text.setAttribute('text-anchor', 'middle');
            text.setAttribute('fill', '#78909c');
            text.setAttribute('font-size', '20');
            text.textContent = options.message || 'Map unavailable — using offline map';
            svg.appendChild(text);

            el.appendChild(svg);

            // Provide a minimal mock `map` object so page scripts don't break
            window.map = {
                _zoom: options.zoom || 16,
                setZoom(z) { this._zoom = z; },
                getZoom() { return this._zoom; },
                setCenter() { /* noop */ },
                addListener() { /* noop */ }
            };
        }

        // Web Speech API for Voice Navigation
        // Voice navigation support
        const voiceEnabled = <?php echo (isset($currentUser['accessibility_mode']) && $currentUser['accessibility_mode']) ? 'true' : 'false'; ?>;
        
        // Initialize speech synthesis
        const synth = window.speechSynthesis;
        
        function speak(text, interrupt = false) {
            if (!voiceEnabled) return;
            
            if (interrupt) {
                synth.cancel();
            }
            
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.rate = <?php echo $_SESSION['voice_speed'] ?? 1.0; ?>;
            utterance.pitch = 1.0;
            utterance.volume = 1.0;
            
            synth.speak(utterance);
        }
        
        // Initialize speech recognition for voice commands
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        let recognition = null;
        
        if (SpeechRecognition && voiceEnabled) {
            recognition = new SpeechRecognition();
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'en-US';
            
            recognition.onresult = function(event) {
                const command = event.results[0][0].transcript.toLowerCase();
                handleVoiceCommand(command);
            };
            
            recognition.onerror = function(event) {
                console.error('Speech recognition error:', event.error);
            };
        }
        
        function startVoiceRecognition() {
            if (recognition) {
                speak('Listening for your command', true);
                recognition.start();
            }
        }
        
        function handleVoiceCommand(command) {
            // Voice command handling
            if (command.includes('search') || command.includes('find')) {
                speak('Opening search');
                window.location.href = 'search.php';
            } else if (command.includes('map')) {
                speak('Opening map');
                window.location.href = 'map.php';
            } else if (command.includes('favorites') || command.includes('bookmarks')) {
                speak('Opening favorites');
                window.location.href = 'favorites.php';
            } else if (command.includes('help') || command.includes('emergency')) {
                speak('Opening help');
                window.location.href = 'help.php';
            } else if (command.includes('settings')) {
                speak('Opening settings');
                window.location.href = 'settings.php';
            } else {
                speak('Command not recognized. Please try again.');
            }
        }
        
        // Announce page on load for accessibility
        window.addEventListener('load', function() {
            if (voiceEnabled) {
                const pageTitle = document.querySelector('h1')?.textContent || 'Campus Navigator';
                speak('Welcome to ' + pageTitle);
            }
        });
    </script>
    
    
</head>
<body>
    <?php if ($flashMessage): ?>
        <div class="flash-message <?php echo $flashMessage['type']; ?>" role="alert">
            <?php echo htmlspecialchars($flashMessage['message']); ?>
        </div>
    <?php endif; ?>
