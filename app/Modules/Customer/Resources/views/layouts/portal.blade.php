<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>High-Speed Hotspot</title>
    <style>
        :root { --primary: #007bff; --dark: #333; --light: #f4f4f4; }
        body { font-family: -apple-system, sans-serif; background: var(--light); color: var(--dark); margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 350px; text-align: center; }
        .btn { display: block; width: 100%; padding: 12px; background: var(--primary); color: #fff; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; font-weight: bold; margin-top: 10px; box-sizing: border-box; }
        .btn-outline { background: transparent; border: 2px solid var(--primary); color: var(--primary); }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 16px; }
        .plan-item { border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 5px; cursor: pointer; transition: 0.2s; }
        .plan-item:hover { border-color: var(--primary); background: #f0f7ff; }
        .loader { border: 4px solid #f3f3f3; border-top: 4px solid var(--primary); border-radius: 50%; width: 30px; height: 30px; animation: spin 1s linear infinite; margin: 20px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="card">
        @yield('content')
    </div>
</body>
</html>