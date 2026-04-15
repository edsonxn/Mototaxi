const fs = require('fs');
let file = fs.readFileSync('index.html', 'utf8');

// Basic replacements
file = file.replace(/openai_api_key/gi, 'gemini_api_key');
file = file.replace(/openai_model/gi, 'gemini_model');
file = file.replace(/openai-api-key/gi, 'gemini-api-key');
file = file.replace(/openai-model/gi, 'gemini-model');
file = file.replace(/obtenerRespuestaOpenAI/g, 'obtenerRespuestaGemini');
file = file.replace(/gpt-5-nano/g, 'gemini-3.1-flash');
file = file.replace(/gpt-4o-mini/g, 'gemini-3.1-flash');
file = file.replace(/GPT-5 Nano/g, 'Gemini 3.1 Flash');
file = file.replace(/OpenAI/g, 'Gemini');
file = file.replace(/OPENAI/g, 'GEMINI');
file = file.replace(/openai/g, 'gemini');
file = file.replace(/api\/gemini-proxy\.php/g, 'api/gemini-proxy.php');

// Replace openai direct fetch fallback with Gemini equivalent
// Instead of complex regex replacing the fetch, let's just find "https://api.gemini.com/v1/chat/completions" assuming it was replaced, 
// NO, wait! The regex didn't replace api.openai.com yet because I used openai -> gemini! 
// Ah! That means it replaced 'https://api.openai.com' -> 'https://api.gemini.com'!

file = file.replace(/https:\/\/api\.gemini\.com\/v1\/chat\/completions/g, 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash:generateContent');

fs.writeFileSync('index.html', file);
