(function () {
    const canvas = document.getElementById('network-bg');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const CONNECTION_DIST = 150;
    let particles = [];

    function resize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    function createParticles() {
        const count = Math.min(100, Math.floor((canvas.width * canvas.height) / 14000));
        particles = [];
        for (let i = 0; i < count; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                vx: (Math.random() - 0.5) * 0.5,
                vy: (Math.random() - 0.5) * 0.5,
                r: Math.random() * 1.5 + 1.5
            });
        }
    }

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        for (let i = 0; i < particles.length; i++) {
            const p = particles[i];
            p.x += p.vx;
            p.y += p.vy;
            if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
            if (p.y < 0 || p.y > canvas.height) p.vy *= -1;

            for (let j = i + 1; j < particles.length; j++) {
                const q = particles[j];
                const dx = p.x - q.x;
                const dy = p.y - q.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < CONNECTION_DIST) {
                    const alpha = 0.15 * (1 - dist / CONNECTION_DIST);
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(q.x, q.y);
                    ctx.strokeStyle = 'rgba(255,255,255,' + alpha + ')';
                    ctx.lineWidth = 1;
                    ctx.stroke();
                }
            }

            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(255,255,255,0.5)';
            ctx.fill();
        }

        requestAnimationFrame(animate);
    }

    window.addEventListener('resize', function () {
        resize();
        createParticles();
    });

    resize();
    createParticles();
    animate();
})();
