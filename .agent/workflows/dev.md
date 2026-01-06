---
description: Start the development server (Backend + Frontend)
---

// turbo
1. Kill any existing processes on port 8000 and run the development environment:
```bash
lsof -ti:8000 | xargs kill -9 2>/dev/null || true && composer dev
```
