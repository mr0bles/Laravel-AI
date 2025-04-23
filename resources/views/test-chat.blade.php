<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat Interface</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .message-enter-active, .message-leave-active {
            transition: all 0.3s ease;
        }

        .message-enter-from, .message-leave-to {
            opacity: 0;
            transform: translateY(20px);
        }

        .typing-indicator span {
            animation: blink 1.4s infinite;
            animation-fill-mode: both;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: .2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: .4s;
        }

        @keyframes blink {
            0% {
                opacity: .2;
            }
            20% {
                opacity: 1;
            }
            100% {
                opacity: .2;
            }
        }

        .tools-used {
            font-size: 0.8em;
            color: #6b7280;
            margin-top: 0.5em;
            padding-top: 0.5em;
            border-top: 1px dashed #e5e7eb;
        }

        .tool-icon {
            display: inline-block;
            width: 16px;
            height: 16px;
            margin-right: 4px;
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-gray-100 h-screen">
<div id="app" class="container mx-auto h-full flex flex-col">
    <div class="flex-1 p-4 flex flex-col max-w-4xl mx-auto w-full h-full">
        <!-- Header -->
        <div class="bg-white rounded-t-lg shadow-lg p-4 border-b">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">AI Chat Interface</h1>
                <div class="flex space-x-4 items-center">
                    <select v-model="selectedModel" class="p-2 border rounded text-sm">
                        <option value="llama3:8b">Llama3 8b</option>
                        <option value="deepseek-coder">Deepseek Coder</option>
                        <option value="qwen2.5:3b">Qwen 2.5 3B</option>
                    </select>
                    <div class="flex items-center space-x-2">
                        <label class="text-sm">Temp:</label>
                        <input type="number" v-model.number="temperature" step="0.1" min="0" max="1"
                               class="w-16 p-1 border rounded text-sm">
                    </div>
                    <div class="flex items-center space-x-2">
                        <label class="text-sm">Top P:</label>
                        <input type="number" v-model.number="topP" step="0.1" min="0" max="1"
                               class="w-16 p-1 border rounded text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="flex-1 bg-white overflow-y-auto p-4" ref="messagesContainer">
            <transition-group name="message">
                <div v-for="(message, index) in messages" :key="index"
                     :class="['mb-4 p-3 rounded-lg max-w-3xl',
                                 message.role === 'user' ? 'ml-auto bg-blue-100' :
                                 message.role === 'system' ? 'bg-gray-100' : 'bg-green-100']">
                    <div class="text-sm text-gray-600 mb-1">@{{ message.role }}:</div>
                    <div class="whitespace-pre-wrap">@{{ message.content }}</div>
                    <div v-if="message.tools" class="tools-used">
                        <svg class="tool-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Tools:
                        <span
                            v-for="(tool, i) in message.tools"
                            :key="i"
                            class="inline-block bg-gray-200 text-gray-800 px-2 py-1 rounded text-xs mr-1"
                            :title="tool.function.description"
                            v-text="tool.function.name"
                        ></span>
                    </div>
                </div>
            </transition-group>
            <div v-if="isLoading" class="p-3 bg-gray-100 rounded-lg inline-block typing-indicator">
                <span>.</span><span>.</span><span>.</span>
            </div>
        </div>

        <!-- Input Area -->
        <div class="bg-white rounded-b-lg shadow-lg p-4 border-t">
            <div class="flex space-x-4">
                <textarea
                    v-model="newMessage"
                    @keydown.enter.prevent="sendMessage"
                    class="flex-1 p-2 border rounded resize-none"
                    placeholder="Type your message..."
                    rows="2"
                ></textarea>
                <button
                    @click="sendMessage"
                    :disabled="isLoading"
                    class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:bg-blue-300"
                >
                    Send
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const {createApp} = Vue

    createApp({
        data() {
            return {
                messages: [
                    {
                        role: 'system',
                        content: 'Hola, ¿como puedo ayudarte?'
                    }
                ],
                newMessage: '',
                isLoading: false,
                selectedModel: 'qwen2.5:3b',
                temperature: 0.7,
                topP: 0.9,
                error: null
            }
        },
        methods: {
            async sendMessage() {
                if (!this.newMessage.trim()) return;

                // Add user message
                this.messages.push({
                    role: 'user',
                    content: this.newMessage.trim()
                });

                const userMessage = this.newMessage.trim();
                this.newMessage = '';
                this.isLoading = true;
                this.error = null;

                try {
                    const response = await axios({
                        method: 'post',
                        url: '/api/v1/llm/chat',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        data: {
                            messages: this.messages,
                            options: {
                                model: this.selectedModel,
                                temperature: this.temperature,
                                top_p: this.topP
                            }
                        }
                    });

                    // Add assistant response
                    if (response.data && response.data.response) {
                        // Extraer las herramientas usadas del contexto
                        let toolsUsed = [];
                        if (response.data.metadata?.messages) {
                            for (const msg of response.data.metadata.messages) {
                                if (msg.content && msg.content.includes('available_tools')) {
                                    try {
                                        const input = msg.content;
                                        const jsonStart = input.indexOf('[');
                                        const jsonStr = input.slice(jsonStart);
                                        toolsUsed = JSON.parse(jsonStr);
                                    } catch (e) {
                                        console.error('Error parsing tools:', e);
                                    }
                                }
                            }
                        }

                        this.messages.push({
                            role: 'assistant',
                            content: response.data.response.message?.content || response.data.response,
                            tools: toolsUsed.length > 0 ? toolsUsed : null
                        });

                    }
                } catch (err) {
                    this.error = err.response?.data || err.message;
                    console.error('Error:', err);
                } finally {
                    this.isLoading = false;
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                }
            },
            scrollToBottom() {
                const container = this.$refs.messagesContainer;
                container.scrollTop = container.scrollHeight;
            }
        }
    }).mount('#app')
</script>
</body>
</html>
