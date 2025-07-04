<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>活体检测</title>
    {load href="/static/css/font-awesome.css" /}
    {load href="/static/js/hls.min.js" /}
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #1a2a6c);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #fff;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            margin: 20px;
        }

        header {
            background: rgba(0, 0, 0, 0.7);
            padding: 20px;
            text-align: center;
            border-bottom: 2px solid #ff5722;
        }

        h1 {
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            margin-bottom: 10px;
            background: linear-gradient(to right, #ff8a00, #e52e71);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .subtitle {
            color: #aaa;
            font-size: clamp(0.9rem, 2vw, 1.1rem);
        }

        .content {
            display: flex;
            padding: 20px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .player-section {
            flex: 1;
            min-width: 300px;
            background: rgba(30, 30, 40, 0.7);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .video-container {
            position: relative;
            width: 100%;
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
            background: #000;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.5);
        }

        #videoPlayer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            outline: none;
        }

        .controls {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
            padding: 15px;
            background: rgba(20, 20, 30, 0.8);
            border-radius: 10px;
        }

        .input-group {
            flex: 1;
            min-width: 300px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #ff9800;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #444;
            border-radius: 8px;
            background: rgba(30, 30, 40, 0.9);
            color: #fff;
            font-size: 16px;
            transition: all 0.3s;
        }

        input:focus {
            border-color: #ff5722;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 87, 34, 0.3);
        }

        .btn-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        button {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(to right, #ff5722, #ff9800);
            color: white;
            font-size: clamp(14px, 2vw, 16px);
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            white-space: nowrap;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 87, 34, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        #loadBtn {
            background: linear-gradient(to right, #2196f3, #21cbf3);
        }

        .info-section {
            flex: 1;
            min-width: 300px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .card {
            background: rgba(30, 30, 40, 0.7);
            border-radius: 15px;
            padding: clamp(15px, 3vw, 20px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .card h2 {
            font-size: clamp(1.2rem, 3vw, 1.5rem);
            color: #ff9800;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #444;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: rgba(255, 152, 0, 0.1);
            border-radius: 8px;
            font-size: clamp(14px, 2vw, 16px);
        }

        .feature i {
            color: #ff9800;
            font-size: clamp(20px, 3vw, 24px);
        }

        .instructions ol {
            padding-left: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .instructions li {
            line-height: 1.6;
        }

        .status {
            margin-top: 20px;
            padding: 15px;
            background: rgba(20, 20, 30, 0.8);
            border-radius: 10px;
            display: none;
        }

        .status.visible {
            display: block;
        }

        .status h3 {
            color: #ff9800;
            margin-bottom: 10px;
        }

        #statusMessage {
            color: #4caf50;
            font-weight: 500;
        }

        #statusMessage.error {
            color: #f44336;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #aaa;
            font-size: 0.9rem;
            border-top: 1px solid #333;
        }

        .quality-selector {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .quality-btn {
            padding: 8px 15px;
            background: rgba(255, 152, 0, 0.2);
            border: 1px solid #ff9800;
            border-radius: 6px;
            color: #ff9800;
            cursor: pointer;
            transition: all 0.3s;
        }

        .quality-btn:hover, .quality-btn.active {
            background: rgba(255, 152, 0, 0.4);
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }

            .content {
                padding: 10px;
            }

            .player-section, .info-section {
                min-width: 100%;
            }

            .btn-group {
                grid-template-columns: 1fr;
            }

            button {
                width: 100%;
            }

            .recording-preview {
                aspect-ratio: 3/4 !important;
                max-width: 100vw !important;
                max-height: 80vh !important;
                width: auto !important;
                height: auto !important;
                margin: 0 auto 20px !important;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #000;
            }
            .recording-preview video {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                border-radius: 10px;
            }
        }

        @media (max-width: 480px) {
            header {
                padding: 15px;
            }

            .controls {
                padding: 10px;
            }

            .card {
                padding: 15px;
            }

            .feature {
                flex-direction: column;
                text-align: center;
            }
        }

        /* 模态框样式 */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: rgba(30, 30, 40, 0.95);
            padding: 20px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            text-align: center;
        }

        .action-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .action-item {
            padding: 15px;
            background: rgba(255, 152, 0, 0.1);
            border: 1px solid #ff9800;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .action-item i {
            font-size: 24px;
            color: #ff9800;
            transition: all 0.3s;
        }

        .action-item:hover {
            background: rgba(255, 152, 0, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 152, 0, 0.2);
        }

        .action-item.active {
            background: rgba(255, 152, 0, 0.3);
            border-color: #ff5722;
            box-shadow: 0 0 15px rgba(255, 87, 34, 0.3);
        }

        .action-item.active i {
            color: #ff5722;
            transform: scale(1.1);
        }

        .action-item.active::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 2px solid #ff5722;
            border-radius: 8px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 87, 34, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(255, 87, 34, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 87, 34, 0);
            }
        }

        .action-item span {
            font-weight: 500;
            transition: all 0.3s;
        }

        .action-item.active span {
            color: #ff5722;
            font-weight: 600;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        .modal-buttons button {
            min-width: 120px;
        }

        .recording-section {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: rgba(30, 30, 40, 0.7);
            border-radius: 15px;
            text-align: center;
            width: 100%;
        }

        .recording-section.visible {
            display: block;
        }

        /* 移动端样式 */
        @media (max-width: 768px) {
            .recording-section {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 20px;
                background: rgba(0, 0, 0, 0.9);
                border-radius: 0;
                z-index: 1000;
                box-sizing: border-box;
            }

            .recording-section.visible {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .recording-info {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 15px;
                align-items: center;
                margin-bottom: 0;
            }

            .selected-actions {
                background: rgba(0, 0, 0, 0.7);
                padding: 15px;
                border-radius: 10px;
                backdrop-filter: blur(5px);
            }

            .action-guide {
                background: rgba(0, 0, 0, 0.7);
                padding: 15px;
                border-radius: 10px;
                backdrop-filter: blur(5px);
                max-width: 400px;
                margin-top: 16px;
            }

            .close-recording {
                position: absolute;
                top: 20px;
                right: 20px;
                background: rgba(0, 0, 0, 0.7);
                color: #fff;
                border: none;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 2;
                transition: all 0.3s;
            }

            .close-recording:hover {
                background: rgba(255, 87, 34, 0.7);
                transform: rotate(90deg);
            }

            .recording-preview {
                margin: 0 auto;
            }

            .recording-controls {
                margin-top: 20px;
                z-index: 2;
            }
        }

        /* 移动端小屏幕适配 */
        @media (max-width: 480px) {
            .recording-section {
                padding: 10px;
            }

            .recording-info {
                top: 10px;
                left: 10px;
                right: 10px;
            }

            .action-guide {
                max-width: 100%;
            }

            .close-recording {
                top: 10px;
                right: 10px;
            }
        }

        .recording-preview {
            width: 100%;
            max-width: 640px;
            aspect-ratio: 16/9;
            margin: 0 auto 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            background: #000;
        }

        .recording-preview video {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        .recording-status {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            display: none;
        }

        .recording-status.visible {
            display: block;
        }

        .recording-status.recording {
            background: rgba(220, 53, 69, 0.7);
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .recording-controls {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 15px;
        }

        .selected-actions {
            margin: 15px 0;
            padding: 10px;
            background: rgba(255, 152, 0, 0.1);
            border-radius: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
        }

        .selected-action-tag {
            padding: 5px 10px;
            background: rgba(255, 152, 0, 0.2);
            border: 1px solid #ff9800;
            border-radius: 15px;
            font-size: 14px;
            color: #ff9800;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .selected-action-tag i {
            font-size: 12px;
            cursor: pointer;
        }

        .action-guide {
            margin: 15px 0;
            padding: 15px;
            background: rgba(255, 152, 0, 0.1);
            border-radius: 8px;
            text-align: left;
        }

        .action-guide h4 {
            color: #ff9800;
            margin-bottom: 10px;
        }

        .action-guide ol {
            padding-left: 20px;
            color: #fff;
        }

        .action-guide li {
            margin-bottom: 8px;
        }

        @media (max-width: 480px) {
            .action-list {
                grid-template-columns: 1fr;
            }

            .modal-buttons {
                flex-direction: column;
            }

            .modal-buttons button {
                width: 100%;
            }
        }

        .action-mask {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 2;
            color: #fff;
            font-size: 2.5rem;
            text-shadow: 0 2px 8px rgba(0,0,0,0.7);
            background: rgba(0,0,0,0.08);
            transition: opacity 0.3s;
        }
        .action-mask .fa-arrow-left,
        .action-mask .fa-arrow-right,
        .action-mask .fa-arrow-down,
        .action-mask .fa-comment,
        .action-mask .fa-eye {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .action-mask.hide {
            opacity: 0;
        }
        .action-mask.show {
            opacity: 1;
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1><i class="fas fa-play-circle"></i> M3U8视频播放器</h1>
        <p class="subtitle">支持HTTP Live Streaming (HLS) 协议，跨浏览器兼容播放</p>
    </header>

    <div class="content">
        <div class="player-section">
            <div class="video-container">
                <video id="videoPlayer" controls></video>
            </div>

            <div class="controls">
                <div class="input-group">
                    <label for="videoUrl"><i class="fas fa-link"></i> M3U8视频地址</label>
                    <input type="text" id="videoUrl" placeholder="输入M3U8视频流地址..."
                           value="https://mjyy-media.zaixian100f.com/5014fe78ecbf71eeb1af4531948c0102/f2eda0a59ad043b28b7a7ba31fb05982-491166293ecc83199aaabfc13888d5a6-fd.m3u8?auth_key=1748498840-474274015b4c4bb09e560ae05841e00f-0-4f73dc0b3f48b4e940bd4d788ac74f4e">
                </div>

                <div class="btn-group">
                    <button id="loadBtn">
                        <i class="fas fa-cloud-download-alt"></i> 加载视频
                    </button>
                    <button id="playBtn">
                        <i class="fas fa-play"></i> 播放/暂停
                    </button>
                    <button id="activeLivenessBtn">
                        <i class="fas fa-user-check"></i> 动作活体检测
                    </button>
                    <button id="silentLivenessBtn">
                        <i class="fas fa-user-shield"></i> 静默活体检测
                    </button>
                </div>
            </div>

            <div class="status" id="statusContainer">
                <h3>检测状态</h3>
                <p id="statusMessage">准备就绪</p>
            </div>

            <!-- 动作活体检测弹窗 -->
            <div id="livenessModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <h2>选择要执行的动作</h2>
                    <p class="text-gray-400 mb-4">请选择需要执行的动作（可多选）</p>
                    <div class="action-list">
                        <div class="action-item" data-action="2">
                            <i class="fas fa-undo"></i>
                            <span>左摇头</span>
                        </div>
                        <div class="action-item" data-action="1">
                            <i class="fas fa-redo"></i>
                            <span>右摇头</span>
                        </div>
                        <div class="action-item" data-action="3">
                            <i class="fas fa-arrow-down"></i>
                            <span>点头</span>
                        </div>
                        <div class="action-item" data-action="4">
                            <i class="fas fa-comment"></i>
                            <span>嘴部动作</span>
                        </div>
                        <div class="action-item" data-action="5">
                            <i class="fas fa-eye"></i>
                            <span>眨眼</span>
                        </div>
                    </div>
                    <div class="modal-buttons">
                        <button id="confirmActionsBtn" class="bg-green-600 hover:bg-green-700">确认选择</button>
                        <button id="closeModalBtn" class="bg-gray-600 hover:bg-gray-700">取消</button>
                    </div>
                    <div id="actionErrorMsg" style="color: #f44336; margin-top: 10px; min-height: 22px;"></div>
                </div>
            </div>
        </div>

        <div class="info-section">
            <div class="card">
                <h2><i class="fas fa-video"></i> 活体检测</h2>
                <p>本系统支持两种活体检测方式：</p>
                <div class="features">
                    <div class="feature">
                        <i class="fas fa-user-check"></i>
                        <span>动作活体检测：通过特定的动作指令进行活体验证</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-user-shield"></i>
                        <span>静默活体检测：无需动作，通过面部特征进行活体验证</span>
                    </div>
                </div>
            </div>

            <!-- 录制区域 -->
            <div id="recordingSection" class="recording-section">
                <!-- 移动端关闭按钮 -->
                <button class="close-recording" id="closeRecordingBtn" style="display: none;">
                    <i class="fas fa-times"></i>
                </button>
                <div class="recording-info">
                    <div class="selected-actions" id="selectedActionsContainer">
                        <!-- 选中的动作将在这里显示 -->
                    </div>
                </div>
                <div class="recording-preview">
                    <video id="cameraPreview" autoplay playsinline muted></video>
                    <div id="actionMask" class="action-mask"></div>
                    <div id="recordingStatus" class="recording-status">准备就绪</div>
                </div>
                <div class="action-guide">
                    <h4>录制说明：</h4>
                    <ol>
                        <li>请确保面部在画面中央</li>
                        <li>按照提示依次完成选定的动作</li>
                        <li>每个动作保持2-3秒</li>
                        <li>动作要自然，幅度适中</li>
                    </ol>
                </div>
                <div class="recording-controls">
                    <button id="startRecordingBtn" class="bg-green-600 hover:bg-green-700">
                        <i class="fas fa-play mr-2"></i>开始录制
                    </button>
                    <button id="stopRecordingBtn" class="bg-red-600 hover:bg-red-700" style="display: none;">
                        <i class="fas fa-stop mr-2"></i>停止录制
                    </button>
                    <button id="cancelRecordingBtn" class="bg-gray-600 hover:bg-gray-700" style="display:none;">
                        <i class="fas fa-times mr-2"></i>取消录制
                    </button>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>© 2025 活体检测系统 | 技术支持：华为云</p>
    </footer>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        const video = document.getElementById('videoPlayer');
        const videoUrlInput = document.getElementById('videoUrl');
        const loadBtn = document.getElementById('loadBtn');
        const playBtn = document.getElementById('playBtn');
        const statusContainer = document.getElementById('statusContainer');
        const statusMessage = document.getElementById('statusMessage');
        let hls;

        // 更新状态信息
        function updateStatus(message, isError = false) {
            statusContainer.classList.add('visible');
            statusMessage.textContent = message;
            statusMessage.className = isError ? 'error' : '';
        }

        // 初始化播放器
        function initPlayer(url) {
            // 清理之前的实例
            if (hls) {
                hls.destroy();
            }

            // 检查浏览器是否原生支持HLS
            if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = url;
                updateStatus('使用浏览器原生HLS支持播放');
            }
            // 使用hls.js作为后备方案
            else if (Hls.isSupported()) {
                hls = new Hls();
                hls.loadSource(url);
                hls.attachMedia(video);

                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    updateStatus('视频加载完成，请点击播放按钮开始播放');
                });

                hls.on(Hls.Events.ERROR, function(event, data) {
                    let errorType = data.type;
                    let errorDetails = data.details;
                    let errorFatal = data.fatal;

                    let errorMsg = `播放错误: ${errorDetails}`;

                    if (errorFatal) {
                        errorMsg += ' (致命错误)';
                    }

                    updateStatus(errorMsg, true);
                    console.error('播放器错误:', errorType, errorDetails);
                });
            } else {
                updateStatus('您的浏览器不支持HLS播放', true);
            }
        }

        // 加载按钮事件
        loadBtn.addEventListener('click', function() {
            const url = videoUrlInput.value.trim();
            if (!url) {
                updateStatus('请输入有效的M3U8地址', true);
                return;
            }

            updateStatus('正在加载视频...');
            initPlayer(url);
        });

        // 播放/暂停按钮事件
        playBtn.addEventListener('click', function() {
            if (video.paused) {
                video.play();
                playBtn.innerHTML = '<i class="fas fa-pause"></i> 暂停';
            } else {
                video.pause();
                playBtn.innerHTML = '<i class="fas fa-play"></i> 播放';
            }
        });

        // 视频播放状态变化
        video.addEventListener('play', function() {
            playBtn.innerHTML = '<i class="fas fa-pause"></i> 暂停';
        });

        video.addEventListener('pause', function() {
            playBtn.innerHTML = '<i class="fas fa-play"></i> 播放';
        });

        // 测试流选择器
        document.querySelectorAll('.quality-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const url = this.getAttribute('data-url');
                videoUrlInput.value = url;

                // 更新活动状态
                document.querySelectorAll('.quality-btn').forEach(b => {
                    b.classList.remove('active');
                });
                this.classList.add('active');

                // 加载视频
                updateStatus('正在加载测试视频...');
                initPlayer(url);
            });
        });

        // 初始化默认测试流
        const defaultUrl = videoUrlInput.value;
        if (defaultUrl) {
            initPlayer(defaultUrl);
        }

        // 活体检测相关功能
        const activeLivenessBtn = document.getElementById('activeLivenessBtn');
        const silentLivenessBtn = document.getElementById('silentLivenessBtn');
        const livenessModal = document.getElementById('livenessModal');
        const confirmActionsBtn = document.getElementById('confirmActionsBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const recordingSection = document.getElementById('recordingSection');
        const selectedActionsContainer = document.getElementById('selectedActionsContainer');
        const actionItems = document.querySelectorAll('.action-item');
        const cameraPreview = document.getElementById('cameraPreview');
        const startRecordingBtn = document.getElementById('startRecordingBtn');
        const stopRecordingBtn = document.getElementById('stopRecordingBtn');
        const recordingStatus = document.getElementById('recordingStatus');

        let mediaRecorder;
        let recordedChunks = [];
        let selectedActions = new Set();
        let stream = null;

        // 动作提示相关
        const actionMask = document.getElementById('actionMask');
        let actionOrder = [];
        let currentActionIndex = 0;
        let actionTimer = null;

        // 动作与图标、提示映射
        const actionTips = {
            '2': { icon: 'fa-arrow-left', text: '请左摇头' },
            '1': { icon: 'fa-arrow-right', text: '请右摇头' },
            '3': { icon: 'fa-arrow-down', text: '请点头' },
            '4': { icon: 'fa-comment', text: '请做嘴部动作' },
            '5': { icon: 'fa-eye', text: '请眨眼' }
        };

        // 显示当前动作提示
        function showActionTip(action) {
            if (!actionTips[action]) {
                actionMask.classList.add('hide');
                actionMask.classList.remove('show');
                actionMask.innerHTML = '';
                return;
            }
            const tip = actionTips[action];
            actionMask.innerHTML = `<i class="fas ${tip.icon}"></i><div style='font-size:1.5rem;margin-top:0.5rem;'>${tip.text}</div>`;
            actionMask.classList.remove('hide');
            actionMask.classList.add('show');
        }
        // 隐藏动作提示
        function hideActionTip() {
            actionMask.classList.add('hide');
            actionMask.classList.remove('show');
        }

        // 显示模态框
        activeLivenessBtn.addEventListener('click', function() {
            livenessModal.style.display = 'flex';
            selectedActions.clear();
            actionItems.forEach(item => item.classList.remove('active'));
            updateSelectedActionsDisplay();
            const actionErrorMsg = document.getElementById('actionErrorMsg');
            if (actionErrorMsg) actionErrorMsg.textContent = '';
        });

        // 选择动作
        actionItems.forEach(item => {
            item.addEventListener('click', function() {
                const action = this.dataset.action;
                if (selectedActions.has(action)) {
                    selectedActions.delete(action);
                    this.classList.remove('active');
                } else {
                    selectedActions.add(action);
                    this.classList.add('active');
                }
                updateSelectedActionsDisplay();
            });
        });

        // 更新选中动作的显示
        function updateSelectedActionsDisplay() {
            selectedActionsContainer.innerHTML = '';
            selectedActions.forEach(action => {
                const actionName = getActionName(action);
                const tag = document.createElement('div');
                tag.className = 'selected-action-tag';
                tag.innerHTML = `
                    <span>${actionName}</span>
                    <i class="fas fa-times" data-action="${action}"></i>
                `;
                selectedActionsContainer.appendChild(tag);
            });

            // 添加删除事件监听
            selectedActionsContainer.querySelectorAll('.fa-times').forEach(icon => {
                icon.addEventListener('click', function() {
                    const action = this.dataset.action;
                    selectedActions.delete(action);
                    actionItems.forEach(item => {
                        if (item.dataset.action === action) {
                            item.classList.remove('active');
                        }
                    });
                    updateSelectedActionsDisplay();

                    // 当所有动作被取消时，关闭录制界面
                    if (selectedActions.size === 0) {
                        if (stream) {
                            stream.getTracks().forEach(track => track.stop());
                            stream = null;
                        }
                        recordingSection.classList.remove('visible');
                        recordingStatus.classList.remove('visible');
                        recordingStatus.classList.remove('recording');
                        updateStatus('已取消所有动作，请重新点击"动作活体检测"按钮选择动作');
                    }
                });
            });
        }

        // 获取动作名称
        function getActionName(action) {
            const actionNames = {
                '2': '左摇头',
                '1': '右摇头',
                '3': '点头',
                '4': '嘴部动作',
                '5': '眨眼'
            };
            return actionNames[action] || action;
        }

        // 确认选择
        confirmActionsBtn.addEventListener('click', async function() {
            const actionErrorMsg = document.getElementById('actionErrorMsg');
            if (selectedActions.size === 0) {
                actionErrorMsg.textContent = '请至少选择一个动作';
                return;
            } else {
                actionErrorMsg.textContent = '';
            }

            try {
                // 初始化摄像头
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        frameRate: { ideal: 30 }
                    },
                    audio: false
                });

                cameraPreview.srcObject = stream;
                await cameraPreview.play(); // 确保视频开始播放
                livenessModal.style.display = 'none';
                recordingSection.classList.add('visible');
                recordingStatus.classList.add('visible');
                
                // 根据屏幕宽度显示/隐藏关闭按钮
                const closeRecordingBtn = document.getElementById('closeRecordingBtn');
                if (window.innerWidth <= 768) {
                    closeRecordingBtn.style.display = 'flex';
                } else {
                    closeRecordingBtn.style.display = 'none';
                }
                
                updateStatus('摄像头已就绪，请按照提示完成动作');
            } catch (error) {
                let errorMessage = '无法访问摄像头: ';
                if (error.name === 'NotAllowedError') {
                    errorMessage += '请允许浏览器访问摄像头';
                } else if (error.name === 'NotFoundError') {
                    errorMessage += '未检测到摄像头设备';
                } else {
                    errorMessage += error.message;
                }
                updateStatus(errorMessage, true);
            }

            // H5界面清除失败原因
            if (window.innerWidth <= 768) {
                const mobileErrorTip = document.getElementById('mobileErrorTip');
                if (mobileErrorTip) {
                    mobileErrorTip.textContent = '';
                    mobileErrorTip.style.display = 'none';
                }
            }
        });

        // 监听窗口大小变化
        window.addEventListener('resize', function() {
            const closeRecordingBtn = document.getElementById('closeRecordingBtn');
            const cancelRecordingBtn = document.getElementById('cancelRecordingBtn');
            if (window.innerWidth <= 768) {
                closeRecordingBtn.style.display = 'flex';
                cancelRecordingBtn.style.display = 'inline-block';
            } else {
                closeRecordingBtn.style.display = 'none';
                cancelRecordingBtn.style.display = 'none';
            }
        });

        // 初始判断移动端显示取消按钮
        window.addEventListener('DOMContentLoaded', function() {
            const cancelRecordingBtn = document.getElementById('cancelRecordingBtn');
            if (window.innerWidth <= 768) {
                cancelRecordingBtn.style.display = 'inline-block';
            } else {
                cancelRecordingBtn.style.display = 'none';
            }
        });

        // 取消录制按钮功能
        document.getElementById('cancelRecordingBtn').addEventListener('click', function() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            recordingSection.classList.remove('visible');
            recordingStatus.classList.remove('visible');
            recordingStatus.classList.remove('recording');
            updateStatus('已取消录制');
        });

        // 开始录制
        let recordStartTime = null;
        startRecordingBtn.addEventListener('click', function() {
            // 清除抓拍图片和结果提示
            let resultImageWrapper = document.getElementById('resultImageWrapper');
            if (resultImageWrapper) {
                let resultImage = document.getElementById('resultImage');
                let resultImageDesc = document.getElementById('resultImageDesc');
                let resultTip = document.getElementById('mobileLivenessResultTip');
                if (resultImage) resultImage.style.display = 'none';
                if (resultImageDesc) resultImageDesc.style.display = 'none';
                if (resultTip) resultTip.style.display = 'none';
                resultImageWrapper.style.display = 'none';
            }
            // 先重置
            recordStartTime = null;

            if (selectedActions.size === 0) {
                updateStatus('请先选择要执行的动作', true);
                return;
            }

            try {
                recordedChunks = [];
                mediaRecorder = new MediaRecorder(stream, {
                    mimeType: 'video/webm;codecs=vp9',
                    videoBitsPerSecond: 2500000
                });

                mediaRecorder.ondataavailable = (e) => {
                    if (e.data.size > 0) {
                        recordedChunks.push(e.data);
                    }
                };

                mediaRecorder.onstop = async () => {
                    recordingStatus.textContent = '处理中...';
                    recordingStatus.classList.remove('recording');
                    const blob = new Blob(recordedChunks, { type: 'video/webm' });

                    // 计算实际录制时长（秒），每次都用最新的 recordStartTime
                    let actualDuration = 0;
                    if (recordStartTime) {
                        actualDuration = (Date.now() - recordStartTime) / 1000;
                    }
                    recordStartTime = null;

                    // 检查视频时长（取实际录制时长和视频元数据的最大值，防止浏览器兼容性问题）
                    const videoForCheck = document.createElement('video');
                    videoForCheck.preload = 'metadata';
                    videoForCheck.src = URL.createObjectURL(blob);
                    await new Promise((resolve, reject) => {
                        videoForCheck.onloadedmetadata = () => resolve();
                        videoForCheck.onerror = reject;
                    });
                    let metaDuration = videoForCheck.duration;
                    URL.revokeObjectURL(videoForCheck.src);
                    // 修正：只用有效的 metaDuration，否则用 actualDuration
                    if (!metaDuration || !isFinite(metaDuration) || isNaN(metaDuration)) {
                        metaDuration = 0;
                    }
                    const duration = Math.max(metaDuration, actualDuration);
                    if (duration < 1) {
                        updateStatus('录制失败：视频时长不能小于1秒', true);
                        recordingStatus.textContent = '视频过短';
                        return;
                    }
                    if (duration > 15) {
                        updateStatus('录制失败：视频时长不能超过15秒', true);
                        recordingStatus.textContent = '视频过长';
                        return;
                    }

                    // 压缩视频，目标2MB以内，最大8MB
                    let finalBlob = blob;
                    let base64 = await blobToBase64(finalBlob);
                    let base64Size = Math.ceil((base64.length - 'data:video/webm;base64,'.length) * 3 / 4);
                    if (base64Size > 8 * 1024 * 1024) {
                        updateStatus('录制失败：视频文件过大（超过8MB），请缩短录制时长或降低分辨率', true);
                        recordingStatus.textContent = '视频过大';
                        // H5界面额外弹出失败原因
                        if (window.innerWidth <= 768) {
                            let mobileErrorTip = document.getElementById('mobileErrorTip');
                            if (!mobileErrorTip) {
                                mobileErrorTip = document.createElement('div');
                                mobileErrorTip.id = 'mobileErrorTip';
                                mobileErrorTip.style.color = '#ff4d4f';
                                mobileErrorTip.style.background = 'rgba(255,255,255,0.95)';
                                mobileErrorTip.style.borderRadius = '8px';
                                mobileErrorTip.style.padding = '12px 16px';
                                mobileErrorTip.style.margin = '16px auto 0';
                                mobileErrorTip.style.fontWeight = 'bold';
                                mobileErrorTip.style.fontSize = '1.1rem';
                                mobileErrorTip.style.maxWidth = '90%';
                                mobileErrorTip.style.textAlign = 'center';
                                recordingSection.appendChild(mobileErrorTip);
                            }
                            mobileErrorTip.textContent = '视频文件过大，请缩短录制时长或降低分辨率后重试';
                            mobileErrorTip.style.display = 'block';
                        }
                        return;
                    }

                    try {
                        updateStatus('正在验证...');
                        const response = await fetch('/activeLiveness', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                video: base64,
                                actions: Array.from(selectedActions)
                            })
                        });

                        const result = await response.json();
                        // 新增：检测结果图片渲染区域和说明
                        let resultImageWrapper = document.getElementById('resultImageWrapper');
                        if (!resultImageWrapper) {
                            resultImageWrapper = document.createElement('div');
                            resultImageWrapper.id = 'resultImageWrapper';
                            resultImageWrapper.style.textAlign = 'center';
                            resultImageWrapper.style.marginTop = '16px';
                            // 文字说明
                            const desc = document.createElement('div');
                            desc.id = 'resultImageDesc';
                            desc.textContent = '下图为检测时抓拍的视频图片';
                            desc.style.color = '#ff9800';
                            desc.style.fontSize = '15px';
                            desc.style.marginBottom = '8px';
                            resultImageWrapper.appendChild(desc);
                            // 图片
                            const img = document.createElement('img');
                            img.id = 'resultImage';
                            img.style.display = 'block';
                            img.style.maxWidth = '320px';
                            img.style.margin = '0 auto';
                            img.style.borderRadius = '10px';
                            img.style.boxShadow = '0 2px 8px rgba(0,0,0,0.2)';
                            resultImageWrapper.appendChild(img);
                            recordingSection.appendChild(resultImageWrapper);
                        }
                        let resultImage = document.getElementById('resultImage');
                        let resultImageDesc = document.getElementById('resultImageDesc');
                        if (result.alive === true) {
                            updateStatus('动作活体检测成功');
                            recordingStatus.textContent = '验证成功';
                            const mobileErrorTip = document.getElementById('mobileErrorTip');
                            if (mobileErrorTip) mobileErrorTip.style.display = 'none';
                            if (result.picture) {
                                resultImage.src = result.picture.startsWith('data:') ? result.picture : 'data:image/jpeg;base64,' + result.picture;
                                resultImageWrapper.style.display = 'block';
                            } else {
                                resultImageWrapper.style.display = 'none';
                            }
                            // 在渲染抓拍图片前，H5界面插入结果提示
                            if (window.innerWidth <= 768) {
                                let resultTip = document.getElementById('mobileLivenessResultTip');
                                if (!resultTip) {
                                    resultTip = document.createElement('div');
                                    resultTip.id = 'mobileLivenessResultTip';
                                    resultTip.style.fontWeight = 'bold';
                                    resultTip.style.fontSize = '1.1rem';
                                    resultTip.style.margin = '0 auto 8px';
                                    resultTip.style.maxWidth = '90%';
                                    resultTip.style.textAlign = 'center';
                                    resultImageWrapper.insertBefore(resultTip, resultImageWrapper.firstChild);
                                }
                                resultTip.textContent = '动作活体检测成功';
                                resultTip.style.color = '#4caf50';
                                resultTip.style.background = 'rgba(76,175,80,0.08)';
                                resultTip.style.display = 'block';
                            } else {
                                // PC端隐藏该tip
                                let resultTip = document.getElementById('mobileLivenessResultTip');
                                if (resultTip) resultTip.style.display = 'none';
                            }
                        } else {
                            let errorMsg = '动作活体检测失败';
                            if (result.error) errorMsg += ': ' + result.error;
                            updateStatus(errorMsg, true);
                            recordingStatus.textContent = '验证失败';
                            // H5界面额外弹出失败原因
                            if (window.innerWidth <= 768 && result.error) {
                                let mobileErrorTip = document.getElementById('mobileErrorTip');
                                if (!mobileErrorTip) {
                                    mobileErrorTip = document.createElement('div');
                                    mobileErrorTip.id = 'mobileErrorTip';
                                    mobileErrorTip.style.color = '#ff4d4f';
                                    mobileErrorTip.style.background = 'rgba(255,255,255,0.95)';
                                    mobileErrorTip.style.borderRadius = '8px';
                                    mobileErrorTip.style.padding = '12px 16px';
                                    mobileErrorTip.style.margin = '16px auto 0';
                                    mobileErrorTip.style.fontWeight = 'bold';
                                    mobileErrorTip.style.fontSize = '1.1rem';
                                    mobileErrorTip.style.maxWidth = '90%';
                                    mobileErrorTip.style.textAlign = 'center';
                                    recordingSection.appendChild(mobileErrorTip);
                                }
                                mobileErrorTip.textContent = '失败原因：' + result.error;
                                mobileErrorTip.style.display = 'block';
                            } else {
                                const mobileErrorTip = document.getElementById('mobileErrorTip');
                                if (mobileErrorTip) mobileErrorTip.style.display = 'none';
                            }
                            if (result.picture) {
                                resultImage.src = result.picture.startsWith('data:') ? result.picture : 'data:image/jpeg;base64,' + result.picture;
                                resultImageWrapper.style.display = 'block';
                            } else {
                                resultImageWrapper.style.display = 'none';
                            }
                        }
                    } catch (error) {
                        updateStatus('验证请求失败: ' + error.message, true);
                        recordingStatus.textContent = '验证失败';
                    }
                };

                mediaRecorder.start();
                // 确保 start 之后再赋值，避免浏览器异步误差
                recordStartTime = Date.now();
                startRecordingBtn.style.display = 'none';
                stopRecordingBtn.style.display = 'inline-block';
                recordingStatus.textContent = '录制中...';
                recordingStatus.classList.add('recording');
                updateStatus('正在录制...');
            } catch (error) {
                updateStatus('录制失败: ' + error.message, true);
            }
        });

        // 停止录制
        stopRecordingBtn.addEventListener('click', function() {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
                startRecordingBtn.style.display = 'inline-block';
                stopRecordingBtn.style.display = 'none';
            }
        });

        // Blob转Base64
        function blobToBase64(blob) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onloadend = () => resolve(reader.result);
                reader.onerror = reject;
                reader.readAsDataURL(blob);
            });
        }

        // 录制时依次提示动作（每3秒自动切换）
        startRecordingBtn.addEventListener('click', function() {
            actionOrder = Array.from(selectedActions);
            currentActionIndex = 0;
            if (actionOrder.length > 0) {
                showActionTip(actionOrder[currentActionIndex]);
                actionTimer = setInterval(() => {
                    currentActionIndex++;
                    if (currentActionIndex < actionOrder.length) {
                        showActionTip(actionOrder[currentActionIndex]);
                    } else {
                        hideActionTip();
                        clearInterval(actionTimer);
                    }
                }, 3000);
            }
        });

        // 停止录制时隐藏提示
        stopRecordingBtn.addEventListener('click', function() {
            hideActionTip();
            if (actionTimer) clearInterval(actionTimer);
        });

        document.getElementById('closeRecordingBtn').addEventListener('click', function() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            recordingSection.classList.remove('visible');
            recordingStatus.classList.remove('visible');
            recordingStatus.classList.remove('recording');
            updateStatus('已取消录制');
        });

        // 取消按钮事件，关闭弹窗并清空选中动作
        closeModalBtn.addEventListener('click', function() {
            livenessModal.style.display = 'none';
            selectedActions.clear();
            actionItems.forEach(item => item.classList.remove('active'));
            updateSelectedActionsDisplay();
            const actionErrorMsg = document.getElementById('actionErrorMsg');
            if (actionErrorMsg) actionErrorMsg.textContent = '';
        });

        // 静默活体检测
        silentLivenessBtn.addEventListener('click', async function() {
            // 如果modal不存在，先插入
            let modal = document.getElementById('silentLivenessModal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'silentLivenessModal';
                modal.style.display = 'none';
                modal.style.position = 'fixed';
                modal.style.top = '0';
                modal.style.left = '0';
                modal.style.width = '100vw';
                modal.style.height = '100vh';
                modal.style.background = 'rgba(0,0,0,0.85)';
                modal.style.zIndex = '2000';
                modal.style.justifyContent = 'center';
                modal.style.alignItems = 'center';
                modal.innerHTML = `
                  <div style="background:#222;padding:24px 16px;border-radius:12px;max-width:95vw;width:360px;text-align:center;position:relative;pointer-events:auto;">
                    <div style="color:#ff9800;font-size:1rem;margin-bottom:8px;">请将人脸对准摄像头并居中</div>
                    <div id="silentLivenessVideoWrap">
                      <video id="silentLivenessVideo" autoplay playsinline style="width:100%;border-radius:8px;"></video>
                    </div>
                    <canvas id="silentLivenessCanvas" style="display:none;width:100%;border-radius:8px;"></canvas>
                    <div id="silentLivenessBtns" style="margin-top:18px;display:flex;justify-content:center;gap:10px;flex-wrap:wrap;">
                      <button id="takeSilentPhotoBtn" style="padding:10px 24px;font-size:1rem;border-radius:6px;background:#ff9800;color:#fff;border:none;">拍照</button>
                      <button id="retakeSilentPhotoBtn" style="display:none;padding:10px 24px;font-size:1rem;border-radius:6px;background:#888;color:#fff;border:none;">重拍</button>
                      <button id="confirmSilentPhotoBtn" style="display:none;padding:10px 24px;font-size:1rem;border-radius:6px;background:#4caf50;color:#fff;border:none;">确认上传</button>
                      <button id="cancelSilentPhotoBtn" style="padding:10px 24px;font-size:1rem;border-radius:6px;background:#f44336;color:#fff;border:none;">取消</button>
                    </div>
                    <div id="silentLivenessError" style="color:#ff4d4f;margin-top:12px;min-height:22px;"></div>
                  </div>
                `;
                document.body.appendChild(modal);
            }
            // 重新获取元素
            const video = document.getElementById('silentLivenessVideo');
            const canvas = document.getElementById('silentLivenessCanvas');
            const errorDiv = document.getElementById('silentLivenessError');
            const cancelBtn = document.getElementById('cancelSilentPhotoBtn');
            let takeBtn = document.getElementById('takeSilentPhotoBtn');
            let retakeBtn = document.getElementById('retakeSilentPhotoBtn');
            let confirmBtn = document.getElementById('confirmSilentPhotoBtn');
            // 检查元素是否都存在
            if (!takeBtn || !retakeBtn || !confirmBtn || !video || !canvas || !errorDiv || !cancelBtn) {
                alert('静默活体检测界面加载失败，请刷新页面重试');
                return;
            }
            let stream = null;
            let photoBase64 = '';
            // 解绑旧事件，防止多次绑定
            takeBtn.onclick = null;
            retakeBtn.onclick = null;
            confirmBtn.onclick = null;
            cancelBtn.onclick = null;
            modal.onmousedown = null;
            // 打开模态
            modal.style.display = 'flex';
            errorDiv.textContent = '';
            canvas.style.display = 'none';
            video.style.display = 'block';
            takeBtn.style.display = 'inline-block';
            retakeBtn.style.display = 'none';
            confirmBtn.style.display = 'none';
            // 打开摄像头
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                    audio: false
                });
                video.srcObject = stream;
                await video.play();
            } catch (err) {
                errorDiv.textContent = '无法访问摄像头：' + (err.message || '');
                return;
            }
            // 拍照
            takeBtn.onclick = async function() {
                // 限制canvas最大分辨率
                let vw = video.videoWidth || 640;
                let vh = video.videoHeight || 480;
                if (vw > 1280) { vh = Math.round(vh * 1280 / vw); vw = 1280; }
                if (vh > 960) { vw = Math.round(vw * 960 / vh); vh = 960; }
                canvas.width = vw;
                canvas.height = vh;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, vw, vh);
                // 自动压缩图片到1MB以内
                let quality = 0.92;
                let dataUrl = canvas.toDataURL('image/jpeg', quality);
                let base64 = dataUrl.split(',')[1].replace(/[^A-Za-z0-9+/=]/g, '');
                let size = Math.ceil((base64.length) * 3 / 4);
                while (size > 1024 * 1024 && quality > 0.5) {
                    quality -= 0.15;
                    dataUrl = canvas.toDataURL('image/jpeg', quality);
                    base64 = dataUrl.split(',')[1].replace(/[^A-Za-z0-9+/=]/g, '');
                    size = Math.ceil((base64.length) * 3 / 4);
                }
                if (size > 2 * 1024 * 1024) {
                    errorDiv.textContent = '图片过大，请靠近摄像头并重拍';
                    return;
                }
                photoBase64 = base64;
                canvas.style.display = 'block';
                video.style.display = 'none';
                takeBtn.style.display = 'none';
                retakeBtn.style.display = 'inline-block';
                confirmBtn.style.display = 'inline-block';
                errorDiv.textContent = '';
            };
            // 重拍
            retakeBtn.onclick = function() {
                canvas.style.display = 'none';
                video.style.display = 'block';
                takeBtn.style.display = 'inline-block';
                retakeBtn.style.display = 'none';
                confirmBtn.style.display = 'none';
                errorDiv.textContent = '';
            };
            // 确认上传
            confirmBtn.onclick = async function() {
                errorDiv.textContent = '';
                updateStatus('正在进行静默活体检测...');
                try {
                    const response = await fetch('/silentLiveness', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ image: photoBase64 })
                    });
                    const result = await response.json();
                    const isMobile = window.innerWidth <= 768;
                    if (result.alive === true) {
                        updateStatus('静默活体检测成功');
                        if (isMobile) {
                            errorDiv.textContent = '检测成功';
                            setTimeout(() => { modal.style.display = 'none'; if (stream) stream.getTracks().forEach(track => track.stop()); }, 1500);
                        } else {
                            modal.style.display = 'none';
                            if (stream) stream.getTracks().forEach(track => track.stop());
                        }
                    } else {
                        let errorMsg = '静默活体检测失败';
                        if (result.error) errorMsg += ': ' + result.error;
                        errorDiv.textContent = errorMsg;
                        updateStatus(errorMsg, true);
                        if (isMobile) {
                            setTimeout(() => { modal.style.display = 'none'; if (stream) stream.getTracks().forEach(track => track.stop()); }, 1500);
                        }
                    }
                } catch (e) {
                    errorDiv.textContent = '静默活体检测接口请求失败: ' + e.message;
                    updateStatus('静默活体检测接口请求失败: ' + e.message, true);
                    if (window.innerWidth <= 768) {
                        setTimeout(() => { modal.style.display = 'none'; if (stream) stream.getTracks().forEach(track => track.stop()); }, 1500);
                    }
                }
            };
            // 取消按钮事件
            cancelBtn.onclick = function(e) {
                e.stopPropagation();
                modal.style.display = 'none';
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
            };
        });
    });
</script>
</body>
</html>