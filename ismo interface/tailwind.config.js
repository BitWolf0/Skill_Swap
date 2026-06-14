/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './**/*.php',
    './assets/js/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        inter: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
        fira: ['Fira Code', 'Courier New', 'monospace'],
      },
      colors: {
        gray: {
          50: '#F8FAFC',
          100: '#F1F5F9',
          200: '#E2E8F0',
          300: '#CBD5E1',
          400: '#94A3B8',
          500: '#64748B',
          600: '#475569',
          700: '#334155',
          800: '#1E293B',
          900: '#0F172A',
        },
      },
      fontSize: {
        'xs': ['0.73rem', { lineHeight: '1.25' }],
        'sm': ['0.82rem', { lineHeight: '1.4' }],
        'base': ['0.9rem', { lineHeight: '1.5' }],
        'lg': ['0.95rem', { lineHeight: '1.5' }],
        'xl': ['1.05rem', { lineHeight: '1.4' }],
        '2xl': ['1.45rem', { lineHeight: '1.25' }],
        '3xl': ['1.6rem', { lineHeight: '1.2' }],
        '4xl': ['1.75rem', { lineHeight: '1.15' }],
      },
      spacing: {
        'sidebar': '248px',
        'topbar': '64px',
        '2': '2px',
        '2.5': '10px',
         '11': '2.75rem',
        '13': '3.25rem',
        '15': '3.75rem',
        '18': '4.5rem',
      },
      borderRadius: {
        'sm': '8px',
        'md': '12px',
        'lg': '16px',
        'xl': '20px',
      },
      boxShadow: {
        'custom-sm': '0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.05)',
        'custom-md': '0 4px 16px rgba(0,0,0,0.10), 0 2px 4px rgba(0,0,0,0.06)',
        'custom-lg': '0 10px 40px rgba(0,0,0,0.14), 0 4px 8px rgba(0,0,0,0.08)',
        'blue-md': '0 4px 12px rgba(37,99,235,0.30)',
        'blue-sm': '0 0 0 3px rgba(37,99,235,0.12)',
        'blue-hover': '0 0 0 3px rgba(37,99,235,0.10)',
        'blue-btn': '0 2px 8px rgba(37,99,235,0.25)',
        'blue-avatar': '0 2px 6px rgba(37,99,235,0.35)',
        'orange-md': '0 4px 14px rgba(249,115,22,0.35)',
        'orange-lg': '0 6px 20px rgba(249,115,22,0.45)',
        'green-btn': '0 4px 12px rgba(34,197,94,0.2)',
        'red-btn': '0 4px 12px rgba(239,68,68,0.2)',
      },
      transitionDuration: {
        '180': '180ms',
      },
      zIndex: {
        '95': '95',
        '100': '100',
        '105': '105',
      },
      animation: {
        'dropIn': 'dropIn 0.18s cubic-bezier(0.4, 0, 0.2, 1)',
        'slideUp': 'slideUp 0.24s cubic-bezier(0.4, 0, 0.2, 1) both',
        'modalIn': 'modalIn 0.2s ease',
      },
      keyframes: {
        dropIn: {
          from: { opacity: '0', transform: 'translateY(-8px) scale(0.97)' },
          to: { opacity: '1', transform: 'translateY(0) scale(1)' },
        },
        slideUp: {
          from: { opacity: '0', transform: 'translateY(12px)' },
          to: { opacity: '1', transform: 'translateY(0)' },
        },
        modalIn: {
          from: { opacity: '0', transform: 'translateY(-20px) scale(0.96)' },
          to: { opacity: '1', transform: 'translateY(0) scale(1)' },
        },
      },
    },
  },
  plugins: [],
}
