/* statistique.js - Simple stats binding and chart rendering */

'use strict';

function bindStats() {
    const stats = {
        demandes: 124,
        resolues: 98,
        competences: 56,
        note: '4.7',
        mentors: 42,
        stagiaires: 286
    };

    const mapping = {
        'stat-demandes': stats.demandes,
        'stat-resolues': stats.resolues,
        'stat-competences': stats.competences,
        'stat-note': stats.note,
        'stat-mentors': stats.mentors,
        'stat-stagiaires': stats.stagiaires
    };

    Object.keys(mapping).forEach((id) => {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = mapping[id];
        }
    });
}

function renderCompetencesChart() {
    const canvas = document.getElementById('competences-chart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const labels = ['React', 'SQL', 'JavaScript', 'Python', 'Git'];
    const values = [42, 36, 31, 24, 18];

    const width = canvas.width;
    const height = canvas.height;
    const padding = 32;
    const maxVal = Math.max(...values);
    const gap = 14;
    const barWidth = (width - padding * 2 - gap * (values.length - 1)) / values.length;

    ctx.clearRect(0, 0, width, height);
    ctx.fillStyle = '#F8FAFC';
    ctx.fillRect(0, 0, width, height);

    values.forEach((val, idx) => {
        const barHeight = (val / maxVal) * (height - padding * 2);
        const x = padding + idx * (barWidth + gap);
        const y = height - padding - barHeight;

        ctx.fillStyle = '#2563EB';
        ctx.fillRect(x, y, barWidth, barHeight);

        ctx.fillStyle = '#475569';
        ctx.font = '12px Inter, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(labels[idx], x + barWidth / 2, height - 10);
    });
}

function sizeCanvas() {
    const canvas = document.getElementById('competences-chart');
    if (!canvas) return;

    const parent = canvas.parentElement;
    if (!parent) return;

    const width = parent.clientWidth;
    canvas.width = Math.max(320, width);
    canvas.height = 220;
}

document.addEventListener('DOMContentLoaded', () => {
    bindStats();
    sizeCanvas();
    renderCompetencesChart();

    window.addEventListener('resize', () => {
        sizeCanvas();
        renderCompetencesChart();
    });
});
