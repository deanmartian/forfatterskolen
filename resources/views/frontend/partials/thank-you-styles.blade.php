<style>
    .ty-wrapper {
        min-height: 70vh;
        display: flex;
        align-items: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #faf8f5 0%, #fdf5f6 100%);
    }
    .ty-card {
        max-width: 720px;
        margin: 0 auto;
        background: #fff;
        border-radius: 16px;
        padding: 60px 50px;
        box-shadow: 0 10px 40px rgba(134, 39, 54, 0.08);
        text-align: center;
    }
    .ty-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #862736 0%, #5e1a26 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 28px;
        box-shadow: 0 8px 24px rgba(134, 39, 54, 0.2);
    }
    .ty-icon svg { width: 40px; height: 40px; color: #fff; }
    .ty-card h1 {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 2.2rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 16px;
        line-height: 1.2;
    }
    .ty-card p {
        font-size: 1.05rem;
        color: #5a5550;
        line-height: 1.7;
        margin-bottom: 24px;
    }
    .ty-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #862736;
        color: #fff !important;
        padding: 14px 32px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.15s;
        box-shadow: 0 4px 12px rgba(134, 39, 54, 0.2);
    }
    .ty-btn:hover {
        background: #9c2e40;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(134, 39, 54, 0.3);
        color: #fff !important;
        text-decoration: none;
    }
    .ty-btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #862736 !important;
        padding: 14px 28px;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 600;
        margin-left: 8px;
    }
    .ty-btn-secondary:hover { text-decoration: underline; color: #862736 !important; }
    @media (max-width: 600px) {
        .ty-card { padding: 40px 28px; }
        .ty-card h1 { font-size: 1.6rem; }
    }
</style>
