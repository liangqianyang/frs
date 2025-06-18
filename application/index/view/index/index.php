<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M3U8视频播放器</title>
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
        }

        .action-item i {
            font-size: 24px;
            color: #ff9800;
        }

        .action-item:hover {
            background: rgba(255, 152, 0, 0.2);
            transform: translateY(-2px);
        }

        .action-item.active {
            background: rgba(255, 152, 0, 0.3);
            border-color: #ff5722;
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
                        <div class="action-item" data-action="1">
                            <i class="fas fa-undo"></i>
                            <span>左摇头</span>
                        </div>
                        <div class="action-item" data-action="2">
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

            <!-- 录制区域 -->
            <!-- 动作录制模块已移除，仅保留在 info-section 下方 -->
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
                <h3>动作录制</h3>
                <div class="selected-actions" id="selectedActionsContainer">
                    <!-- 选中的动作将在这里显示 -->
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
                <div class="recording-preview">
                    <video id="cameraPreview" autoplay playsinline muted></video>
                    <div id="actionMask" class="action-mask"></div>
                    <div id="recordingStatus" class="recording-status">准备就绪</div>
                </div>
                <div class="recording-controls">
                    <button id="startRecordingBtn" class="bg-green-600 hover:bg-green-700">
                        <i class="fas fa-play mr-2"></i>开始录制
                    </button>
                    <button id="stopRecordingBtn" class="bg-red-600 hover:bg-red-700" style="display: none;">
                        <i class="fas fa-stop mr-2"></i>停止录制
                    </button>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>© 2023 活体检测系统 | 技术支持：ThinkPHP</p>
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
                    updateStatus('视频加载完成，准备播放');
                    video.play();
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
            '1': { icon: 'fa-arrow-left', text: '请左摇头' },
            '2': { icon: 'fa-arrow-right', text: '请右摇头' },
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
                });
            });
        }

        // 获取动作名称
        function getActionName(action) {
            const actionNames = {
                '1': '左摇头',
                '2': '右摇头',
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
        });

        // 关闭模态框时清理资源
        closeModalBtn.addEventListener('click', function() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            livenessModal.style.display = 'none';
            recordingSection.classList.remove('visible');
            recordingStatus.classList.remove('visible');
            recordingStatus.classList.remove('recording');
            selectedActions.clear();
            actionItems.forEach(item => item.classList.remove('active'));
        });

        // 开始录制
        startRecordingBtn.addEventListener('click', function() {
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
                    const compressedBlob = await compressVideo(blob);
                    const base64 = await blobToBase64(compressedBlob);

                    try {
                        updateStatus('正在验证...');
                        const response = await fetch('/index/index/activeLiveness', {
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
                        if (result.code === 0) {
                            updateStatus('动作活体检测成功');
                            recordingStatus.textContent = '验证成功';
                        } else {
                            updateStatus('动作活体检测失败: ' + result.msg, true);
                            recordingStatus.textContent = '验证失败';
                        }
                    } catch (error) {
                        updateStatus('验证请求失败: ' + error.message, true);
                        recordingStatus.textContent = '验证失败';
                    }
                };

                mediaRecorder.start();
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

        // 视频压缩函数
        async function compressVideo(blob) {
            return new Promise((resolve, reject) => {
                const video = document.createElement('video');
                video.src = URL.createObjectURL(blob);

                video.onloadedmetadata = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    // 设置压缩参数
                    const targetSize = 1024 * 1024; // 目标大小 1MB
                    const quality = 0.7; // 压缩质量

                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;

                    video.oncanplay = () => {
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        canvas.toBlob((blob) => {
                            resolve(blob);
                        }, 'video/webm', quality);
                    };

                    video.play();
                };

                video.onerror = reject;
            });
        }

        // Blob转Base64
        function blobToBase64(blob) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onloadend = () => resolve(reader.result.split(',')[1]);
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
    });
</script>
</body>
</html>