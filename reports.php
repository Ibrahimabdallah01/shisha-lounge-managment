<?php
// reports.php - Complete Reports System
error_reporting(0);
ini_set('display_errors', 0);

// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Function to send JSON response
function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

// Function to send error response
function sendError($message, $status = 400) {
    sendResponse([
        'error' => true,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ], $status);
}

// Include database config
if (!file_exists('config/database.php')) {
    sendError("Database configuration file missing");
}

try {
    include_once 'config/database.php';
    $database = new Database();
    $db = $database->connect();
    
    if (!$db) {
        sendError("Database connection failed");
    }
} catch (Exception $e) {
    sendError("Database connection error");
}

// Get report type from query parameter
$report_type = $_GET['report_type'] ?? '';
$date = $_GET['date'] ?? date('Y-m-d');
$month = $_GET['month'] ?? date('n');
$year = $_GET['year'] ?? date('Y');

try {
    switch ($report_type) {
        case 'daily':
            // Daily Report
            $report_data = [];
            
            // Get daily revenue
            $stmt = $db->prepare("SELECT 
                SUM(amount) as total_revenue, 
                COUNT(*) as transactions,
                AVG(amount) as average_transaction
                FROM payments 
                WHERE transaction_date = ?");
            $stmt->execute([$date]);
            $revenue_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get top flavors for the day (based on payments by customers with favorite flavors)
            $stmt = $db->prepare("SELECT 
                c.favorite_flavor as name,
                COUNT(p.id) as orders,
                SUM(p.amount) as revenue
                FROM payments p 
                LEFT JOIN customers c ON p.customer_name = c.name 
                WHERE p.transaction_date = ? AND c.favorite_flavor IS NOT NULL
                GROUP BY c.favorite_flavor 
                ORDER BY orders DESC 
                LIMIT 5");
            $stmt->execute([$date]);
            $top_flavors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get payment methods breakdown
            $stmt = $db->prepare("SELECT 
                payment_method,
                COUNT(*) as count,
                SUM(amount) as total
                FROM payments 
                WHERE transaction_date = ?
                GROUP BY payment_method");
            $stmt->execute([$date]);
            $payment_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $report_data = [
                'date' => $date,
                'revenue' => [
                    'total_revenue' => (float)($revenue_data['total_revenue'] ?? 0),
                    'transactions' => (int)($revenue_data['transactions'] ?? 0),
                    'average_transaction' => (float)($revenue_data['average_transaction'] ?? 0)
                ],
                'top_flavors' => $top_flavors,
                'payment_methods' => $payment_methods
            ];
            
            sendResponse($report_data);
            break;
            
        case 'weekly':
            // Weekly Report
            $start_date = date('Y-m-d', strtotime($date . ' -6 days'));
            $end_date = $date;
            
            // Weekly revenue
            $stmt = $db->prepare("SELECT 
                transaction_date,
                SUM(amount) as daily_revenue,
                COUNT(*) as daily_transactions
                FROM payments 
                WHERE transaction_date BETWEEN ? AND ?
                GROUP BY transaction_date
                ORDER BY transaction_date");
            $stmt->execute([$start_date, $end_date]);
            $daily_breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Weekly totals
            $stmt = $db->prepare("SELECT 
                SUM(amount) as total_revenue,
                COUNT(*) as total_transactions,
                AVG(amount) as average_transaction,
                COUNT(DISTINCT customer_name) as unique_customers
                FROM payments 
                WHERE transaction_date BETWEEN ? AND ?");
            $stmt->execute([$start_date, $end_date]);
            $weekly_totals = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $report_data = [
                'period' => "Week ending $end_date",
                'start_date' => $start_date,
                'end_date' => $end_date,
                'totals' => [
                    'total_revenue' => (float)($weekly_totals['total_revenue'] ?? 0),
                    'total_transactions' => (int)($weekly_totals['total_transactions'] ?? 0),
                    'average_transaction' => (float)($weekly_totals['average_transaction'] ?? 0),
                    'unique_customers' => (int)($weekly_totals['unique_customers'] ?? 0)
                ],
                'daily_breakdown' => $daily_breakdown
            ];
            
            sendResponse($report_data);
            break;
            
        case 'monthly':
            // Monthly Report
            $stmt = $db->prepare("SELECT 
                SUM(amount) as total_revenue,
                COUNT(*) as total_transactions,
                AVG(amount) as average_transaction,
                COUNT(DISTINCT customer_name) as unique_customers
                FROM payments 
                WHERE MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?");
            $stmt->execute([$month, $year]);
            $monthly_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Daily breakdown for the month
            $stmt = $db->prepare("SELECT 
                DAY(transaction_date) as day,
                SUM(amount) as revenue,
                COUNT(*) as transactions
                FROM payments 
                WHERE MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?
                GROUP BY DAY(transaction_date)
                ORDER BY DAY(transaction_date)");
            $stmt->execute([$month, $year]);
            $daily_breakdown = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Top customers for the month
            $stmt = $db->prepare("SELECT 
                customer_name,
                COUNT(*) as visits,
                SUM(amount) as total_spent
                FROM payments 
                WHERE MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?
                GROUP BY customer_name
                ORDER BY total_spent DESC
                LIMIT 10");
            $stmt->execute([$month, $year]);
            $top_customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $report_data = [
                'month' => $month,
                'year' => $year,
                'month_name' => date('F Y', mktime(0, 0, 0, $month, 1, $year)),
                'totals' => [
                    'total_revenue' => (float)($monthly_data['total_revenue'] ?? 0),
                    'total_transactions' => (int)($monthly_data['total_transactions'] ?? 0),
                    'average_transaction' => (float)($monthly_data['average_transaction'] ?? 0),
                    'unique_customers' => (int)($monthly_data['unique_customers'] ?? 0)
                ],
                'daily_breakdown' => $daily_breakdown,
                'top_customers' => $top_customers
            ];
            
            sendResponse($report_data);
            break;
            
        case 'flavors':
            // Flavor Analysis Report
            $stmt = $db->query("SELECT 
                f.name,
                f.stock,
                f.min_stock,
                f.price,
                f.popularity,
                CASE 
                    WHEN f.stock <= f.min_stock THEN 'Low Stock'
                    WHEN f.stock <= (f.min_stock * 1.5) THEN 'Medium Stock'
                    ELSE 'Good Stock'
                END as stock_status
                FROM flavors f
                ORDER BY f.popularity DESC");
            $flavors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get sales data for flavors (from customer preferences)
            $stmt = $db->query("SELECT 
                c.favorite_flavor as flavor,
                COUNT(*) as customer_count,
                SUM(c.total_spent) as total_revenue
                FROM customers c
                WHERE c.favorite_flavor IS NOT NULL
                GROUP BY c.favorite_flavor
                ORDER BY total_revenue DESC");
            $flavor_sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Low stock alerts
            $stmt = $db->query("SELECT name, stock, min_stock 
                FROM flavors 
                WHERE stock <= min_stock
                ORDER BY stock ASC");
            $low_stock = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $report_data = [
                'flavors' => $flavors,
                'flavor_sales' => $flavor_sales,
                'low_stock_alerts' => $low_stock,
                'total_flavors' => count($flavors),
                'low_stock_count' => count($low_stock)
            ];
            
            sendResponse($report_data);
            break;
            
        case 'inventory':
            // Inventory Status Report
            $stmt = $db->query("SELECT 
                item_name,
                stock,
                unit,
                min_stock,
                last_updated,
                CASE 
                    WHEN stock <= min_stock THEN 'Critical'
                    WHEN stock <= (min_stock * 1.5) THEN 'Low'
                    ELSE 'Good'
                END as status
                FROM inventory 
                ORDER BY 
                    CASE 
                        WHEN stock <= min_stock THEN 1
                        WHEN stock <= (min_stock * 1.5) THEN 2
                        ELSE 3
                    END,
                    stock ASC");
            $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Summary stats
            $critical_items = array_filter($inventory, function($item) {
                return $item['status'] === 'Critical';
            });
            
            $low_items = array_filter($inventory, function($item) {
                return $item['status'] === 'Low';
            });
            
            $report_data = [
                'inventory' => $inventory,
                'summary' => [
                    'total_items' => count($inventory),
                    'critical_items' => count($critical_items),
                    'low_stock_items' => count($low_items),
                    'good_stock_items' => count($inventory) - count($critical_items) - count($low_items)
                ]
            ];
            
            sendResponse($report_data);
            break;
            
        case 'employees':
            // Employee Performance Report
            $stmt = $db->query("SELECT 
                name,
                position,
                shift,
                services_count,
                salary,
                hire_date,
                status,
                ROUND(services_count / (DATEDIFF(NOW(), hire_date) + 1), 2) as daily_average
                FROM employees 
                WHERE status = 'active'
                ORDER BY services_count DESC");
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate totals
            $total_salary = array_sum(array_column($employees, 'salary'));
            $total_services = array_sum(array_column($employees, 'services_count'));
            
            $report_data = [
                'employees' => $employees,
                'summary' => [
                    'total_employees' => count($employees),
                    'total_monthly_salary' => (float)$total_salary,
                    'total_services_count' => (int)$total_services,
                    'average_services_per_employee' => count($employees) > 0 ? round($total_services / count($employees), 2) : 0
                ]
            ];
            
            sendResponse($report_data);
            break;
            
        case 'dashboard_summary':
            // Dashboard Summary for quick overview
            $today = date('Y-m-d');
            
            // Today's stats
            $stmt = $db->prepare("SELECT 
                COUNT(*) as transactions,
                SUM(amount) as revenue,
                COUNT(DISTINCT customer_name) as customers
                FROM payments 
                WHERE transaction_date = ?");
            $stmt->execute([$today]);
            $today_stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // This month stats
            $stmt = $db->prepare("SELECT 
                COUNT(*) as transactions,
                SUM(amount) as revenue
                FROM payments 
                WHERE MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?");
            $stmt->execute([date('n'), date('Y')]);
            $month_stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Low stock count
            $stmt = $db->query("SELECT COUNT(*) as count FROM flavors WHERE stock <= min_stock");
            $low_stock_flavors = $stmt->fetch()['count'];
            
            $stmt = $db->query("SELECT COUNT(*) as count FROM inventory WHERE stock <= min_stock");
            $low_stock_inventory = $stmt->fetch()['count'];
            
            $report_data = [
                'today' => [
                    'revenue' => (float)($today_stats['revenue'] ?? 0),
                    'transactions' => (int)($today_stats['transactions'] ?? 0),
                    'customers' => (int)($today_stats['customers'] ?? 0)
                ],
                'month' => [
                    'revenue' => (float)($month_stats['revenue'] ?? 0),
                    'transactions' => (int)($month_stats['transactions'] ?? 0)
                ],
                'alerts' => [
                    'low_stock_flavors' => (int)$low_stock_flavors,
                    'low_stock_inventory' => (int)$low_stock_inventory,
                    'total_alerts' => (int)($low_stock_flavors + $low_stock_inventory)
                ]
            ];
            
            sendResponse($report_data);
            break;
            
        default:
            sendError("Unknown report type: $report_type");
    }
    
} catch (Exception $e) {
    sendError("Error generating report: " . $e->getMessage());
}
?>