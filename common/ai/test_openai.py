from fastapi import FastAPI, Query
from fastapi.middleware.cors import CORSMiddleware
from llama_index.core import VectorStoreIndex, SimpleDirectoryReader, Settings
from llama_index.embeddings.huggingface import HuggingFaceEmbedding
from llama_index.core.llms.mock import MockLLM  # LLM giả lập (offline)
import uvicorn
import os

# === 🌐 Khởi tạo ứng dụng FastAPI ===
app = FastAPI(title="AI Quy Chế Offline", version="1.0")

# === 🔑 Bật CORS để có thể gọi từ view Yii ===
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # hoặc ['http://localhost:8080'] nếu muốn giới hạn
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# === 🧠 Cấu hình mô hình offline ===
Settings.llm = MockLLM(max_tokens=512)
Settings.embed_model = HuggingFaceEmbedding(model_name="sentence-transformers/all-MiniLM-L6-v2")

# === 📂 Nạp dữ liệu từ thư mục quy chế ===
data_dir = r"D:\WWW\YII\yii_ttb\common\ai\data\quyche"

if not os.path.exists(data_dir):
    raise FileNotFoundError(f"❌ Không tìm thấy thư mục dữ liệu: {data_dir}")

print(f"📂 Đang nạp dữ liệu từ: {data_dir}")

try:
    documents = SimpleDirectoryReader(data_dir).load_data()
    index = VectorStoreIndex.from_documents(documents)
    query_engine = index.as_query_engine()
    print(f"✅ Đã nạp {len(documents)} tài liệu quy chế.")
except Exception as e:
    query_engine = None
    print(f"⚠️ Lỗi khi nạp dữ liệu: {e}")

# === 🌐 API Endpoints ===
@app.get("/")
def home():
    return {"message": "✅ AI Quy chế offline đang chạy!"}

@app.get("/ask")
def ask(question: str = Query(..., description="Câu hỏi cần hỏi AI")):
    """
    Ví dụ:
    http://127.0.0.1:8000/ask?question=hình thức thi hết học phần
    """
    if not question.strip():
        return {"error": "❌ Vui lòng nhập câu hỏi hợp lệ."}

    if query_engine is None:
        return {"question": question, "answer": "❌ Lỗi: chưa nạp dữ liệu quy chế."}

    try:
        answer = query_engine.query(question)
        return {"question": question, "answer": str(answer)}
    except Exception as e:
        return {"question": question, "answer": f"⚠️ Lỗi khi trả lời: {e}"}

# === 🚀 Chạy ứng dụng ===
if __name__ == "__main__":
    print("🚀 AI Quy chế offline đang chạy tại: http://127.0.0.1:8000")
    uvicorn.run(app, host="127.0.0.1", port=8000)
