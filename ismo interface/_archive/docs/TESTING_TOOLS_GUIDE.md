# ISMO-SkillSwap Testing & Benchmark Tools Guide

## Overview

Three comprehensive testing and benchmarking tools have been created to monitor and improve project performance:

1. **benchmark.php** - PHP-based performance metrics
2. **css_fixer.py** - CSS optimization and validation
3. **test_functionality.py** - Comprehensive page testing

---

## Tool 1: benchmark.php

### Purpose
Comprehensive PHP-based benchmarking of database, filesystem, and assets.

### Features
- Database connection testing
- Database query performance analysis
- File accessibility verification
- CSS and JS file analysis
- Page structure validation
- API endpoint discovery

### Usage

#### Via Browser
```
http://localhost/Skill_Swap/ismo%20interface/tools/benchmark.php
```

#### Via Command Line
```bash
php tools/benchmark.php > benchmark_report.html
```

### Output
- Beautiful HTML report with metrics
- Saved to `reports/benchmark_*.html`
- Shows database performance
- Lists all files with size and accessibility status

### Metrics Provided
- Database connection time (ms)
- Query execution times
- File sizes and accessibility
- CSS file count and total size
- JS file count and total size
- API endpoint status
- Page structure inventory

---

## Tool 2: css_fixer.py

### Purpose
Automated CSS scanning, validation, and optimization.

### Features
- Empty CSS rule detection and removal
- Missing semicolon detection
- Hex color collection for CSS variables
- Duplicate selector identification
- Font declaration validation
- Whitespace optimization
- Detailed JSON report generation

### Usage

```bash
cd /opt/lampp/htdocs/Skill_Swap/ismo\ interface
python3 tools/css_fixer.py .
```

### Output
- Scans all CSS files in `assets/css/`
- Fixes identified issues
- Generates `reports/css_report.json`
- Console output showing progress

### Report Contents
```json
{
  "title": "ISMO-SkillSwap CSS Analysis Report",
  "timestamp": "2026-06-01T15:51:50",
  "total_files": 22,
  "summary": {
    "total_size_kb": 298.33,
    "total_lines": 16148,
    "total_issues_found": 226,
    "files_needing_fixes": 20
  },
  "files": {
    "dashboard.css": {
      "size_kb": 65.2,
      "lines": 3165,
      "issues": {
        "empty_rules": 0,
        "hex_colors_found": 73,
        "duplicate_selectors": 10,
        "font_issues": 2
      },
      "status": "NEEDS_FIX"
    }
  }
}
```

### Interpretation

- **hex_colors_found:** Convert these to CSS variables at `:root`
- **duplicate_selectors:** Consolidate these selectors
- **empty_rules:** Already removed
- **font_issues:** Verify font declarations

---

## Tool 3: test_functionality.py

### Purpose
Comprehensive testing of all pages, assets, and APIs.

### Features
- Tests all public pages
- Tests static assets (CSS, JS)
- Measures response times
- Validates page structure
- Checks for forms, titles, CSS/JS inclusion
- Generates HTML and JSON reports

### Usage

```bash
cd /opt/lampp/htdocs/Skill_Swap/ismo\ interface
python3 tools/test_functionality.py
```

### Output Example
```
🧪 ISMO-SkillSwap Comprehensive Functionality Test Suite
============================================================

📄 Testing Public Pages...
  ✓ Login Page: 23.21ms (13.61KB)
  ✓ Registration: 7.39ms (13.61KB)

👨‍🎓 Testing Stagiaire Pages...
  ✗ Stagiaire Dashboard: HTTP 404 (requires auth)
  ...

📦 Testing Static Assets...
  ✓ CSS: Dashboard: 69.72KB
  ✓ JS: Dashboard: 10.45KB
  ...
```

### Reports Generated

1. **HTML Report:** `functionality_test_YYYY-MM-DD_HH-MM-SS.html`
   - Visual dashboard
   - Test results table
   - Color-coded status
   - Performance metrics

2. **JSON Report:** `functionality_test_YYYY-MM-DD_HH-MM-SS.json`
   ```json
   {
     "title": "ISMO-SkillSwap Functionality Test Report",
     "timestamp": "2026-06-01T15:55:19",
     "summary": {
       "total_tests": 27,
       "passed": 10,
       "failed": 17,
       "duration_ms": 164.66
     },
     "results": {
       "Login Page": {
         "status": "PASS",
         "status_code": 200,
         "duration_ms": 23.21,
         "size_kb": 13.61
       }
     }
   }
   ```

---

## Interpreting Test Results

### Status Codes

| Status | Meaning | Action |
|--------|---------|--------|
| PASS (200) | Page loads successfully | ✅ OK |
| FAIL (404) | Page not found | Check URL path |
| FAIL (403) | Access forbidden | May require authentication |
| FAIL (500) | Server error | Check PHP errors |
| TIMEOUT | No response | Check server status |

### Response Times

| Time | Status | Notes |
|------|--------|-------|
| < 10ms | ✅ Excellent | Great performance |
| 10-50ms | ✅ Good | Acceptable |
| 50-200ms | ⚠️ Fair | Monitor |
| > 200ms | ❌ Poor | Needs optimization |

### Asset Sizes

| Asset | Size | Status |
|-------|------|--------|
| CSS file | < 50 KB | ✅ Good |
| CSS file | 50-100 KB | ⚠️ Consider minification |
| CSS file | > 100 KB | ❌ Needs optimization |
| JS file | < 30 KB | ✅ Good |
| JS file | > 50 KB | ⚠️ Consider minification |

---

## Automated Testing Schedule

### Recommended Testing Frequency

1. **Before Deployment**
   - Run all three tools
   - Review all reports
   - Fix any critical issues

2. **Weekly**
   - Run `test_functionality.py`
   - Check response times
   - Monitor file sizes

3. **Monthly**
   - Run `css_fixer.py`
   - Review CSS optimization
   - Plan refactoring

4. **Quarterly**
   - Run full `benchmark.php`
   - Database performance review
   - Comprehensive optimization

---

## Integration with CI/CD

### GitHub Actions Example

```yaml
name: ISMO-SkillSwap Benchmark

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Set up PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      
      - name: Run Benchmark
        run: php tools/benchmark.php > benchmark.html
      
      - name: Run CSS Fixer
        run: python3 tools/css_fixer.py .
      
      - name: Run Functionality Tests
        run: python3 tools/test_functionality.py
```

---

## Troubleshooting

### benchmark.php Issues

**Problem:** "Database connection failed"
- **Solution:** Verify MySQL is running: `systemctl start mysql`
- **Check:** `/opt/lampp/sbin/mysqld` process

**Problem:** "File not found"
- **Solution:** Check file permissions: `chmod 755 backend/`

### css_fixer.py Issues

**Problem:** "ModuleNotFoundError"
- **Solution:** Already uses only built-in modules

**Problem:** "Permission denied"
- **Solution:** Run as owner: `sudo chown -R $USER:$USER /opt/lampp/htdocs/`

### test_functionality.py Issues

**Problem:** "Connection refused"
- **Solution:** Start Apache: `/opt/lampp/bin/apachectl start`

**Problem:** "Timeout"
- **Solution:** Increase timeout in script: `timeout=20` in Request()

---

## Customization

### Adding New Tests

Edit `test_functionality.py`:

```python
# Add to test suite
new_pages = [
    ('pages_custom/new_page.php', 'New Page'),
]

for path, name in new_pages:
    result = self.test_page(path, name)
```

### Custom CSS Rules

Edit `css_fixer.py` to add custom checks:

```python
def check_custom_rule(self, content):
    """Add your custom CSS validation"""
    issues = []
    if 'old-property' in content:
        issues.append('old_property_deprecated')
    return issues
```

---

## Reports Location

All reports are saved to:
```
/opt/lampp/htdocs/Skill_Swap/ismo interface/reports/
```

- `benchmark_*.html` - Performance metrics
- `css_report.json` - CSS analysis
- `functionality_test_*.html` - Test results (HTML)
- `functionality_test_*.json` - Test results (JSON)

---

## Performance Targets

### Recommended Targets

| Metric | Target | Current |
|--------|--------|---------|
| Page Load Time | < 50ms | 6.1ms |
| CSS Size | < 200 KB | 298 KB |
| JS Size | < 150 KB | 156 KB |
| Test Pass Rate | 100% | 37% (auth-dependent) |
| Static Asset Load | < 10ms | 8.5ms |

---

## Next Steps

1. ✅ Review generated reports in `/reports/`
2. ✅ Check `BENCHMARK_REPORT_2026-06-01.md`
3. ✅ Implement CSS optimization recommendations
4. ✅ Set up automated testing schedule
5. ✅ Monitor performance trends

---

## Support & Maintenance

### Regular Maintenance Tasks

**Weekly:**
- Check error logs
- Review performance metrics
- Monitor page load times

**Monthly:**
- Run CSS optimization
- Review database performance
- Update security headers

**Quarterly:**
- Full benchmark assessment
- Performance optimization
- Dependency updates

---

**Document Version:** 1.0  
**Created:** 2026-06-01  
**Tools Version:** 1.0  
**Last Updated:** 2026-06-01 15:55:19
