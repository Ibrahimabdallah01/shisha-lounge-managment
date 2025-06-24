<?php
// api.php - Clean JSON-only API
// Turn off all output buffering and error display for clean JSON
ob_clean();
error_reporting(0);
ini_set('display_errors', 0);

// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Function to send JSON response and exit
function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

// Function to send error response and exit
function sendError($message, $status = 400) {
    sendResponse([
        'error' => true,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ], $status);
}

// Check if required files exist
if (!file_exists('config/database.php')) {
    sendError("Database config file missing");
}

// Include required files
try {
    include_once 'config/database.php';
    include_once 'models/Customer.php';
    include_once 'models/Flavor.php';
    include_once 'models/Inventory.php';
    include_once 'models/Payment.php';
    include_once 'models/Employee.php';
} catch (Exception $e) {
    sendError("Error loading system files");
}

// Initialize database connection
try {
    $database = new Database();
    $db = $database->connect();
    
    if (!$db) {
        sendError("Database connection failed");
    }
} catch (Exception $e) {
    sendError("Database connection error");
}

$request_method = $_SERVER["REQUEST_METHOD"];

try {
    if ($request_method == 'POST') {
        // Handle POST requests
        $input = file_get_contents("php://input");
        $data = json_decode($input);
        
        if (!$data || !isset($data->action)) {
            sendError("Invalid request data");
        }
        
        switch ($data->action) {
            case 'add_customer':
                if (empty($data->name) || empty($data->flavor) || empty($data->amount)) {
                    sendError("Tafadhali jaza taarifa zote");
                }
                
                $customer = new Customer($db);
                $customer->name = $data->name;
                $customer->visits = 1;
                $customer->last_visit = date('Y-m-d');
                $customer->favorite_flavor = $data->flavor;
                $customer->total_spent = $data->amount;
                $customer->status = 'active';
                
                if ($customer->addCustomer()) {
                    // Add payment record
                    $payment = new Payment($db);
                    $payment->customer_name = $data->name;
                    $payment->amount = $data->amount;
                    $payment->payment_method = $data->payment_method ?? 'Cash';
                    $payment->transaction_date = date('Y-m-d');
                    $payment->transaction_time = date('H:i:s');
                    $payment->addPayment();
                    
                    sendResponse(['message' => 'Mteja ameongezwa kikamilifu']);
                } else {
                    sendError("Hitilafu imetokea");
                }
                break;

            case 'add_flavor':
                if (empty($data->name) || empty($data->stock) || empty($data->price)) {
                    sendError("Tafadhali jaza taarifa zote");
                }
                
                $flavor = new Flavor($db);
                $flavor->name = $data->name;
                $flavor->stock = $data->stock;
                $flavor->min_stock = $data->min_stock ?? 10;
                $flavor->price = $data->price;
                
                if ($flavor->addFlavor()) {
                    sendResponse(['message' => 'Flavor imeongezwa kikamilifu']);
                } else {
                    sendError("Hitilafu imetokea");
                }
                break;

            case 'update_flavor_stock':
                if (empty($data->id) || !isset($data->stock)) {
                    sendError("Data haijatosheleza");
                }
                
                $flavor = new Flavor($db);
                if ($flavor->updateStock($data->id, $data->stock)) {
                    sendResponse(['message' => 'Stock imebadilishwa']);
                } else {
                    sendError("Hitilafu imetokea");
                }
                break;

            case 'add_inventory':
                if (empty($data->item_name) || empty($data->stock) || empty($data->unit)) {
                    sendError("Tafadhali jaza taarifa zote");
                }
                
                $inventory = new Inventory($db);
                $inventory->item_name = $data->item_name;
                $inventory->stock = $data->stock;
                $inventory->unit = $data->unit;
                $inventory->min_stock = $data->min_stock ?? 10;
                
                if ($inventory->addItem()) {
                    sendResponse(['message' => 'Kifaa kimeongezwa kikamilifu']);
                } else {
                    sendError("Hitilafu imetokea");
                }
                break;

            case 'update_inventory_stock':
                if (empty($data->id) || !isset($data->stock)) {
                    sendError("Data haijatosheleza");
                }
                
                $inventory = new Inventory($db);
                if ($inventory->updateStock($data->id, $data->stock)) {
                    sendResponse(['message' => 'Stock imebadilishwa']);
                } else {
                    sendError("Hitilafu imetokea");
                }
                break;

            case 'add_employee':
                if (empty($data->name) || empty($data->position) || empty($data->salary)) {
                    sendError("Tafadhali jaza taarifa zote");
                }
                
                $employee = new Employee($db);
                $employee->name = $data->name;
                $employee->position = $data->position;
                $employee->shift = $data->shift ?? 'Mchana';
                $employee->salary = $data->salary;
                
                if ($employee->addEmployee()) {
                    sendResponse(['message' => 'Mfanyakazi ameongezwa kikamilifu']);
                } else {
                    sendError("Hitilafu imetokea");
                }
                break;

            case 'update_employee_salary':
                if (empty($data->id) || empty($data->salary)) {
                    sendError("Data haijatosheleza");
                }
                
                $employee = new Employee($db);
                if ($employee->updateSalary($data->id, $data->salary)) {
                    sendResponse(['message' => 'Mshahara umebadilishwa']);
                } else {
                    sendError("Hitilafu imetokea");
                }
                break;

            default:
                sendError("Kitendo hakijulikani");
        }
        
    } else if ($request_method == 'GET') {
        // Handle GET requests
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'get_customers':
                $customer = new Customer($db);
                $stmt = $customer->getAllCustomers();
                $customers = [];
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $customers[] = [
                        "id" => (int)$row['id'],
                        "name" => $row['name'],
                        "visits" => (int)$row['visits'],
                        "last_visit" => $row['last_visit'],
                        "favorite_flavor" => $row['favorite_flavor'],
                        "total_spent" => (float)$row['total_spent'],
                        "status" => $row['status']
                    ];
                }
                
                sendResponse($customers);
                break;

            case 'get_flavors':
                $flavor = new Flavor($db);
                $stmt = $flavor->getAllFlavors();
                $flavors = [];
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $flavors[] = [
                        "id" => (int)$row['id'],
                        "name" => $row['name'],
                        "stock" => (int)$row['stock'],
                        "min_stock" => (int)$row['min_stock'],
                        "price" => (float)$row['price'],
                        "popularity" => (int)$row['popularity']
                    ];
                }
                
                sendResponse($flavors);
                break;

            case 'get_inventory':
                $inventory = new Inventory($db);
                $stmt = $inventory->getAllItems();
                $items = [];
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $items[] = [
                        "id" => (int)$row['id'],
                        "item_name" => $row['item_name'],
                        "stock" => (int)$row['stock'],
                        "unit" => $row['unit'],
                        "min_stock" => (int)$row['min_stock'],
                        "last_updated" => $row['last_updated']
                    ];
                }
                
                sendResponse($items);
                break;

            case 'get_payments':
                $payment = new Payment($db);
                $stmt = $payment->getAllPayments();
                $payments = [];
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $payments[] = [
                        "id" => (int)$row['id'],
                        "customer_name" => $row['customer_name'],
                        "amount" => (float)$row['amount'],
                        "payment_method" => $row['payment_method'],
                        "transaction_date" => $row['transaction_date'],
                        "transaction_time" => $row['transaction_time']
                    ];
                }
                
                sendResponse($payments);
                break;

            case 'get_employees':
                $employee = new Employee($db);
                $stmt = $employee->getAllEmployees();
                $employees = [];
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $employees[] = [
                        "id" => (int)$row['id'],
                        "name" => $row['name'],
                        "position" => $row['position'],
                        "shift" => $row['shift'],
                        "services_count" => (int)$row['services_count'],
                        "salary" => (float)$row['salary'],
                        "hire_date" => $row['hire_date'],
                        "status" => $row['status'] ?? 'active'
                    ];
                }
                
                sendResponse($employees);
                break;

            case 'get_daily_stats':
                $payment = new Payment($db);
                $today = date('Y-m-d');
                $daily_revenue = $payment->getDailyRevenue($today);
                
                // Count today's customers (customers who made payments today)
                $stmt = $db->prepare("SELECT COUNT(DISTINCT customer_name) as count FROM payments WHERE transaction_date = ?");
                $stmt->execute([$today]);
                $today_customers = $stmt->fetch()['count'];
                
                $stats = [
                    "daily_revenue" => (float)$daily_revenue,
                    "total_customers" => (int)$today_customers,
                    "today_date" => $today
                ];
                
                sendResponse($stats);
                break;

            case 'get_low_stock_alerts':
                $flavor = new Flavor($db);
                $stmt = $flavor->getLowStockFlavors();
                $low_stock_flavors = [];
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $low_stock_flavors[] = [
                        "id" => (int)$row['id'],
                        "name" => $row['name'],
                        "stock" => (int)$row['stock'],
                        "min_stock" => (int)$row['min_stock']
                    ];
                }
                
                sendResponse($low_stock_flavors);
                break;

            case 'test':
                sendResponse([
                    'status' => 'success',
                    'message' => 'API inafanya kazi vizuri',
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
                break;

            default:
                sendError("Kitendo hakijulikani: $action");
        }
        
    } else {
        sendError("HTTP method haijaidhinishwa", 405);
    }
    
} catch (Exception $e) {
    sendError("Hitilafu ya server", 500);
}
?>