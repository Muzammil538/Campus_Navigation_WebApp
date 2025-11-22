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
    
    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAPS_API_KEY; ?>&libraries=places,geometry"></script>
    
    <!-- Web Speech API for Voice Navigation -->
    <script>
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
    
    <?php if (isset($customCSS)): ?>
        <link rel="stylesheet" href="css/<?php echo $customCSS; ?>">
    <?php endif; ?>
</head>
<body>
    <?php if ($flashMessage): ?>
        <div class="flash-message <?php echo $flashMessage['type']; ?>" role="alert">
            <?php echo htmlspecialchars($flashMessage['message']); ?>
        </div>
    <?php endif; ?>
