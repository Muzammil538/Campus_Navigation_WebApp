    <!-- Voice Navigation Button (Accessibility) -->
    <?php if ($currentUser && isset($currentUser['accessibility_mode']) && $currentUser['accessibility_mode']): ?>
    <button class="voice-nav-btn" onclick="startVoiceRecognition()" aria-label="Voice command">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M12 1a3 3 0 013 3v8a3 3 0 11-6 0V4a3 3 0 013-3z" stroke="currentColor" stroke-width="2"/>
            <path d="M19 10v2a7 7 0 01-14 0v-2M12 19v4M8 23h8" stroke="currentColor" stroke-width="2"/>
        </svg>
    </button>
    
    <style>
        .voice-nav-btn {
            position: fixed;
            bottom: 100px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            cursor: pointer;
            z-index: 9999;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .voice-nav-btn:hover {
            transform: scale(1.1);
        }
        
        .flash-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
        }
        
        .flash-message.success {
            background: #4caf50;
            color: white;
        }
        
        .flash-message.error {
            background: #f44336;
            color: white;
        }
        
        .flash-message.info {
            background: #2196f3;
            color: white;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
    <?php endif; ?>
    
    <?php if (isset($customJS)): ?>
        <script src="js/<?php echo $customJS; ?>"></script>
    <?php endif; ?>
</body>
</html>
