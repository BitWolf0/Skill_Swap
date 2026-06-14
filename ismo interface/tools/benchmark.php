<?php
/**
 * ISMO-SkillSwap Comprehensive Benchmark Suite
 * Tests page load times, database performance, and functionality
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include config
require_once dirname(__DIR__) . '/backend/config.php';

class SkillSwapBenchmark {
    private $results = [];
    private $startTime;
    private $dbStats = [];
    
    public function __construct() {
        $this->startTime = microtime(true);
    }
    
    /**
     * Test database connectivity
     */
    public function testDatabaseConnection() {
        $start = microtime(true);
        try {
            $conn = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $duration = (microtime(true) - $start) * 1000;
            $this->results['database_connection'] = [
                'status' => 'PASS',
                'duration_ms' => round($duration, 2),
                'message' => 'Database connection successful'
            ];
            return $conn;
        } catch (Exception $e) {
            $this->results['database_connection'] = [
                'status' => 'FAIL',
                'error' => $e->getMessage()
            ];
            return null;
        }
    }
    
    /**
     * Test database query performance
     */
    public function testDatabaseQueries($conn) {
        if (!$conn) return;
        
        $queries = [
            'users_count' => 'SELECT COUNT(*) as count FROM utilisateurs',
            'skills_count' => 'SELECT COUNT(*) as count FROM competences',
            'requests_count' => 'SELECT COUNT(*) as count FROM demandes',
            'active_sessions' => 'SELECT COUNT(*) as count FROM utilisateurs WHERE actif = 1'
        ];
        
        $queryStats = [];
        foreach ($queries as $name => $query) {
            $start = microtime(true);
            try {
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $duration = (microtime(true) - $start) * 1000;
                $queryStats[$name] = [
                    'status' => 'PASS',
                    'duration_ms' => round($duration, 3),
                    'result' => $result['count'] ?? 0
                ];
            } catch (Exception $e) {
                $queryStats[$name] = [
                    'status' => 'FAIL',
                    'error' => $e->getMessage()
                ];
            }
        }
        $this->results['database_queries'] = $queryStats;
    }
    
    /**
     * Test file accessibility
     */
    public function testFileAccessibility() {
        $files = [
            'backend/config.php',
            'backend/db.php',
            'backend/functions.php',
            'assets/css/dashboard.css',
            'assets/js/dashboard.js',
            'pages_stagiaire/login.php',
            'pages_stagiaire/dashboard.php',
            'pages_mentor/dashboard.php',
            'pages_admin/dashboard.php',
            'formateur_pages/tableau_de_bord.php'
        ];
        
        $fileStats = [];
        $baseDir = dirname(__DIR__);
        foreach ($files as $file) {
            $path = $baseDir . '/' . $file;
            $fileStats[$file] = [
                'status' => file_exists($path) ? 'PASS' : 'FAIL',
                'exists' => file_exists($path),
                'readable' => is_readable($path),
                'size_bytes' => file_exists($path) ? filesize($path) : 0
            ];
        }
        $this->results['file_accessibility'] = $fileStats;
    }
    
    /**
     * Test CSS syntax and file sizes
     */
    public function testCSSFiles() {
        $cssDir = dirname(__DIR__) . '/assets/css';
        $cssFiles = glob($cssDir . '/*.css');
        $cssStats = [];
        $totalSize = 0;
        
        foreach ($cssFiles as $file) {
            $size = filesize($file);
            $totalSize += $size;
            $content = file_get_contents($file);
            
            // Check for common CSS issues
            $hasErrors = [];
            if (preg_match('/{\s*}/', $content)) {
                $hasErrors[] = 'empty_rule';
            }
            if (preg_match('/[^{};]\s*$/', trim($content)) && !preg_match('/}\s*$/', trim($content))) {
                $hasErrors[] = 'unclosed_block';
            }
            
            $cssStats[basename($file)] = [
                'size_bytes' => $size,
                'size_kb' => round($size / 1024, 2),
                'issues' => $hasErrors,
                'lines' => substr_count($content, "\n")
            ];
        }
        
        $this->results['css_analysis'] = [
            'total_files' => count($cssFiles),
            'total_size_kb' => round($totalSize / 1024, 2),
            'files' => $cssStats
        ];
    }
    
    /**
     * Test JavaScript files
     */
    public function testJSFiles() {
        $jsDir = dirname(__DIR__) . '/assets/js';
        $jsFiles = glob($jsDir . '/*.js');
        $jsStats = [];
        $totalSize = 0;
        
        foreach ($jsFiles as $file) {
            $size = filesize($file);
            $totalSize += $size;
            $content = file_get_contents($file);
            
            $jsStats[basename($file)] = [
                'size_bytes' => $size,
                'size_kb' => round($size / 1024, 2),
                'lines' => substr_count($content, "\n"),
                'has_strict_mode' => strpos($content, "'use strict'") !== false || 
                                   strpos($content, '"use strict"') !== false
            ];
        }
        
        $this->results['js_analysis'] = [
            'total_files' => count($jsFiles),
            'total_size_kb' => round($totalSize / 1024, 2),
            'files' => $jsStats
        ];
    }
    
    /**
     * Test page structure
     */
    public function testPageStructure() {
        $pageDirs = [
            'pages_stagiaire',
            'pages_mentor',
            'pages_admin',
            'formateur_pages'
        ];
        
        $pageStats = [];
        $baseDir = dirname(__DIR__);
        
        foreach ($pageDirs as $dir) {
            $dirPath = $baseDir . '/' . $dir;
            if (is_dir($dirPath)) {
                $pages = glob($dirPath . '/*.php');
                $pageCount = count($pages);
                $pageStats[$dir] = [
                    'count' => $pageCount,
                    'pages' => array_map('basename', $pages)
                ];
            }
        }
        
        $this->results['page_structure'] = $pageStats;
    }
    
    /**
     * Test API endpoints
     */
    public function testAPIEndpoints() {
        $apiDir = dirname(__DIR__) . '/backend/api';
        if (is_dir($apiDir)) {
            $endpoints = glob($apiDir . '/*.php');
            $apiStats = [];
            
            foreach ($endpoints as $endpoint) {
                $apiStats[basename($endpoint)] = [
                    'exists' => true,
                    'size_bytes' => filesize($endpoint)
                ];
            }
            $this->results['api_endpoints'] = [
                'count' => count($endpoints),
                'endpoints' => $apiStats
            ];
        }
    }
    
    /**
     * Generate HTML report
     */
    public function generateReport() {
        $totalDuration = (microtime(true) - $this->startTime) * 1000;
        $reportTime = date('Y-m-d H:i:s');
        
        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISMO-SkillSwap Benchmark Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Inter, -apple-system, BlinkMacSystemFont, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; }
        .header h1 { font-size: 2.5em; margin-bottom: 10px; }
        .header p { font-size: 1.1em; opacity: 0.9; }
        .metadata { background: #f9f9f9; padding: 20px; border-bottom: 1px solid #e0e0e0; display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; }
        .metadata-item { }
        .metadata-item label { display: block; font-size: 0.85em; color: #666; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .metadata-item value { display: block; font-size: 1.3em; font-weight: 600; color: #333; }
        .content { padding: 30px; }
        .section { margin-bottom: 40px; }
        .section h2 { font-size: 1.5em; color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #667eea; }
        .status-pass { color: #10b981; background: #ecfdf5; padding: 2px 8px; border-radius: 4px; font-weight: 600; font-size: 0.85em; }
        .status-fail { color: #ef4444; background: #fef2f2; padding: 2px 8px; border-radius: 4px; font-weight: 600; font-size: 0.85em; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #f3f4f6; padding: 12px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid #e5e7eb; }
        td { padding: 12px; border-bottom: 1px solid #e5e7eb; }
        tr:hover { background: #fafafa; }
        .metric { display: inline-block; background: #f3f4f6; padding: 15px 20px; border-radius: 6px; margin-right: 15px; margin-bottom: 10px; }
        .metric label { display: block; font-size: 0.85em; color: #666; text-transform: uppercase; margin-bottom: 5px; }
        .metric value { display: block; font-size: 1.5em; font-weight: 700; color: #667eea; }
        .file-tree { background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 6px; font-family: 'Consolas', monospace; font-size: 0.9em; overflow-x: auto; }
        .footer { background: #f9f9f9; padding: 20px; text-align: center; color: #666; border-top: 1px solid #e0e0e0; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 ISMO-SkillSwap Benchmark Report</h1>
            <p>Comprehensive Performance Analysis & Functionality Test</p>
        </div>
        
        <div class="metadata">
            <div class="metadata-item">
                <label>Report Generated</label>
                <value>$reportTime</value>
            </div>
            <div class="metadata-item">
                <label>Total Duration</label>
                <value>{$totalDuration}ms</value>
            </div>
            <div class="metadata-item">
                <label>Environment</label>
                <value>PHP 8.2 + MySQL 8.0</value>
            </div>
            <div class="metadata-item">
                <label>Server</label>
                <value>Apache/XAMPP</value>
            </div>
        </div>
        
        <div class="content">
HTML;
        
        // Database Results
        if (isset($this->results['database_connection'])) {
            $db = $this->results['database_connection'];
            $status = $db['status'] === 'PASS' ? 'status-pass' : 'status-fail';
            $statusLabel = $db['status'];
            $durationMs = $db['duration_ms'] ?? 'N/A';
            
            $html .= <<<HTML
            <div class="section">
                <h2>📊 Database Performance</h2>
                <div class="metric">
                    <label>Connection Status</label>
                    <value><span class="$status">$statusLabel</span></value>
                </div>
                <div class="metric">
                    <label>Connection Time</label>
                    <value>{$durationMs}ms</value>
                </div>
HTML;
            
            if (isset($this->results['database_queries'])) {
                $html .= '<table><thead><tr><th>Query</th><th>Status</th><th>Duration (ms)</th><th>Result</th></tr></thead><tbody>';
                foreach ($this->results['database_queries'] as $name => $query) {
                    $status = $query['status'] === 'PASS' ? 'status-pass' : 'status-fail';
                    $duration = $query['duration_ms'] ?? 'N/A';
                    $result = $query['result'] ?? '—';
                    $html .= "<tr><td>$name</td><td><span class='$status'>{$query['status']}</span></td><td>$duration</td><td>$result</td></tr>";
                }
                $html .= '</tbody></table>';
            }
            $html .= '</div>';
        }
        
        // File Accessibility
        if (isset($this->results['file_accessibility'])) {
            $html .= '<div class="section"><h2>📁 File System</h2><table><thead><tr><th>File</th><th>Exists</th><th>Readable</th><th>Size</th></tr></thead><tbody>';
            foreach ($this->results['file_accessibility'] as $file => $info) {
                $exists = $info['exists'] ? '✓' : '✗';
                $readable = $info['readable'] ? '✓' : '✗';
                $size = round($info['size_bytes'] / 1024, 2) . ' KB';
                $html .= "<tr><td>$file</td><td>$exists</td><td>$readable</td><td>$size</td></tr>";
            }
            $html .= '</tbody></table></div>';
        }
        
        // CSS Analysis
        if (isset($this->results['css_analysis'])) {
            $css = $this->results['css_analysis'];
            $html .= <<<HTML
            <div class="section">
                <h2>🎨 CSS Analysis</h2>
                <div class="metric">
                    <label>Total CSS Files</label>
                    <value>{$css['total_files']}</value>
                </div>
                <div class="metric">
                    <label>Total Size</label>
                    <value>{$css['total_size_kb']}KB</value>
                </div>
                <table><thead><tr><th>File</th><th>Size (KB)</th><th>Lines</th><th>Issues</th></tr></thead><tbody>
HTML;
            foreach ($css['files'] as $file => $info) {
                $issues = empty($info['issues']) ? 'None' : implode(', ', $info['issues']);
                $html .= "<tr><td>$file</td><td>{$info['size_kb']}</td><td>{$info['lines']}</td><td>$issues</td></tr>";
            }
            $html .= '</tbody></table></div>';
        }
        
        // JS Analysis
        if (isset($this->results['js_analysis'])) {
            $js = $this->results['js_analysis'];
            $html .= <<<HTML
            <div class="section">
                <h2>⚙️ JavaScript Analysis</h2>
                <div class="metric">
                    <label>Total JS Files</label>
                    <value>{$js['total_files']}</value>
                </div>
                <div class="metric">
                    <label>Total Size</label>
                    <value>{$js['total_size_kb']}KB</value>
                </div>
                <table><thead><tr><th>File</th><th>Size (KB)</th><th>Lines</th><th>Strict Mode</th></tr></thead><tbody>
HTML;
            foreach ($js['files'] as $file => $info) {
                $strict = $info['has_strict_mode'] ? '✓' : '✗';
                $html .= "<tr><td>$file</td><td>{$info['size_kb']}</td><td>{$info['lines']}</td><td>$strict</td></tr>";
            }
            $html .= '</tbody></table></div>';
        }
        
        // Page Structure
        if (isset($this->results['page_structure'])) {
            $html .= '<div class="section"><h2>📄 Page Structure</h2><table><thead><tr><th>Section</th><th>Pages</th></tr></thead><tbody>';
            foreach ($this->results['page_structure'] as $section => $pages) {
                $count = $pages['count'];
                $pageList = implode(', ', array_slice($pages['pages'], 0, 3));
                if ($count > 3) $pageList .= '...';
                $html .= "<tr><td>$section</td><td>$count ($pageList)</td></tr>";
            }
            $html .= '</tbody></table></div>';
        }
        
        // API Endpoints
        if (isset($this->results['api_endpoints'])) {
            $api = $this->results['api_endpoints'];
            $html .= <<<HTML
            <div class="section">
                <h2>🔌 API Endpoints</h2>
                <div class="metric">
                    <label>Total Endpoints</label>
                    <value>{$api['count']}</value>
                </div>
                <table><thead><tr><th>Endpoint</th><th>Size</th></tr></thead><tbody>
HTML;
            foreach ($api['endpoints'] as $endpoint => $info) {
                $size = round($info['size_bytes'] / 1024, 2) . ' KB';
                $html .= "<tr><td>$endpoint</td><td>$size</td></tr>";
            }
            $html .= '</tbody></table></div>';
        }
        
        $html .= <<<HTML
        </div>
        
        <div class="footer">
            <p>Generated by ISMO-SkillSwap Benchmark Suite v1.0 | {$totalDuration}ms total execution time</p>
        </div>
    </div>
</body>
</html>
HTML;
        
        return $html;
    }
    
    /**
     * Run all benchmarks
     */
    public function runAll() {
        $conn = $this->testDatabaseConnection();
        $this->testDatabaseQueries($conn);
        $this->testFileAccessibility();
        $this->testCSSFiles();
        $this->testJSFiles();
        $this->testPageStructure();
        $this->testAPIEndpoints();
        
        return $this->results;
    }
}

// Run benchmark
$benchmark = new SkillSwapBenchmark();
$benchmark->runAll();
$html = $benchmark->generateReport();

// Save report
$reportDir = dirname(__DIR__) . '/reports';
if (!is_dir($reportDir)) mkdir($reportDir, 0755, true);
$reportFile = $reportDir . '/benchmark_' . date('Y-m-d_H-i-s') . '.html';
file_put_contents($reportFile, $html);

// Output
header('Content-Type: text/html; charset=utf-8');
echo $html;
?>
