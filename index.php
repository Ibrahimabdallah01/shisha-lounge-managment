<?php require_once 'auth_check.php'; ?>


<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shisha Lounge Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            color: white;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .nav-tabs {
            display: flex;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 10px;
            margin-bottom: 30px;
            overflow-x: auto;
        }

        .nav-tab {
            flex: 1;
            padding: 15px 20px;
            background: transparent;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 10px;
            margin: 0 5px;
            transition: all 0.3s ease;
            white-space: nowrap;
            min-width: 120px;
        }

        .nav-tab:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .nav-tab.active {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 3rem;
            margin-bottom: 15px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 1.1rem;
        }

        .content-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: linear-gradient(45deg, #56ab2f, #a8e6cf);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            color: white;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 5px solid;
        }

        .alert-warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }

        .alert-success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .table th {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            font-weight: 600;
        }

        .table tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-warning {
            background: #ffc107;
            color: #212529;
        }

        .badge-primary {
            background: #007bff;
            color: white;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(45deg, #667eea, #764ba2);
            transition: width 0.3s ease;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .floating-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        @media (max-width: 768px) {
            .nav-tabs {
                flex-direction: column;
            }
            
            .nav-tab {
                margin: 5px 0;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <!-- Header with Logout Button -->
<div class="header">
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <div>
            <h1><i class="fas fa-smoking"></i> Shisha Lounge Management</h1>
            <p>Mfumo wa Kusimamia Biashara ya Shisha</p>
        </div>
        <div class="user-section" style="display: flex; align-items: center; gap: 15px;">
            <div class="user-info" style="text-align: right;">
                <p style="margin: 0; font-size: 0.9rem; opacity: 0.9;">Karibu,</p>
                <p style="margin: 0; font-weight: bold;"><?php echo htmlspecialchars($currentUser['email']); ?></p>
            </div>
            <button onclick="logout()" class="logout-btn" style="
                background: rgba(255, 255, 255, 0.2);
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 8px;
                cursor: pointer;
                font-size: 0.9rem;
                font-weight: 600;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
            ">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </button>
        </div>
    </div>
</div>

        <!-- Navigation -->
        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showTab('dashboard')">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </button>
            <button class="nav-tab" onclick="showTab('customers')">
                <i class="fas fa-users"></i> Wateja
            </button>
            <button class="nav-tab" onclick="showTab('flavors')">
                <i class="fas fa-smoking"></i> Flavors
            </button>
            <button class="nav-tab" onclick="showTab('inventory')">
                <i class="fas fa-boxes"></i> Stock
            </button>
            <button class="nav-tab" onclick="showTab('payments')">
                <i class="fas fa-money-bill-wave"></i> Malipo
            </button>
            <button class="nav-tab" onclick="showTab('employees')">
                <i class="fas fa-user-friends"></i> Wafanyakazi
            </button>
            <button class="nav-tab" onclick="showTab('reports')">
                <i class="fas fa-chart-bar"></i> Ripoti
            </button>
        </div>

        <!-- Dashboard Tab -->
        <div id="dashboard" class="tab-content active">
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-number" id="todayCustomers"><div class="loading"></div></div>
                    <div class="stat-label">Wateja Leo</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-money-bill-wave"></i>
                    <div class="stat-number" id="todayRevenue"><div class="loading"></div></div>
                    <div class="stat-label">Mapato Leo</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-fire"></i>
                    <div class="stat-number" id="charcoalUsed"><div class="loading"></div></div>
                    <div class="stat-label">Mkaa Uliotumika</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-user-check"></i>
                    <div class="stat-number" id="activeEmployees"><div class="loading"></div></div>
                    <div class="stat-label">Wafanyakazi Kazini</div>
                </div>
            </div>

            <!-- Alerts -->
            <div id="alerts"></div>

            <!-- Recent Activity -->
            <div class="content-card">
                <h3><i class="fas fa-chart-line"></i> Shughuli za Hivi Karibuni</h3>
                <div id="recentActivity"><div class="loading"></div></div>
            </div>
        </div>

        <!-- Customers Tab -->
        <div id="customers" class="tab-content">
            <div class="content-card">
                <h3><i class="fas fa-user-plus"></i> Ongeza Mteja Mpya</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Jina la Mteja</label>
                        <input type="text" id="customerName" class="form-control" placeholder="Ingiza jina">
                    </div>
                    <div class="form-group">
                        <label>Flavor</label>
                        <select id="customerFlavor" class="form-control">
                            <option value="">Chagua Flavor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kiasi (TZS)</label>
                        <input type="number" id="customerAmount" class="form-control" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label>Njia ya Malipo</label>
                        <select id="paymentMethod" class="form-control">
                            <option value="Cash">Cash</option>
                            <option value="M-Pesa">M-Pesa</option>
                            <option value="Card">Card</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn-primary" onclick="addCustomer()">
                    <i class="fas fa-plus"></i> Ongeza Mteja
                </button>
            </div>

            <div class="content-card">
                <h3><i class="fas fa-list"></i> Orodha ya Wateja</h3>
                <table class="table" id="customersTable">
                    <thead>
                        <tr>
                            <th>Jina</th>
                            <th>Ziara</th>
                            <th>Tarehe ya Mwisho</th>
                            <th>Flavor ya Mwisho</th>
                            <th>Jumla Imetumia</th>
                            <th>Hali</th>
                        </tr>
                    </thead>
                    <tbody id="customersTableBody">
                        <tr><td colspan="6"><div class="loading"></div> Inapakia data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Flavors Tab -->
        <div id="flavors" class="tab-content">
            <div class="content-card">
                <h3><i class="fas fa-plus-circle"></i> Ongeza Flavor Mpya</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Jina la Flavor</label>
                        <input type="text" id="flavorName" class="form-control" placeholder="Mfano: Blue Mint">
                    </div>
                    <div class="form-group">
                        <label>Idadi ya Stock</label>
                        <input type="number" id="flavorStock" class="form-control" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label>Bei (TZS)</label>
                        <input type="number" id="flavorPrice" class="form-control" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label>Kiwango cha Chini</label>
                        <input type="number" id="flavorMinStock" class="form-control" placeholder="10">
                    </div>
                </div>
                <button class="btn btn-primary" onclick="addFlavor()">
                    <i class="fas fa-plus"></i> Ongeza Flavor
                </button>
            </div>

            <div class="content-card">
                <h3><i class="fas fa-smoking"></i> Orodha ya Flavors</h3>
                <table class="table" id="flavorsTable">
                    <thead>
                        <tr>
                            <th>Jina</th>
                            <th>Stock</th>
                            <th>Bei</th>
                            <th>Umaarufu</th>
                            <th>Hali</th>
                            <th>Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="flavorsTableBody">
                        <tr><td colspan="6"><div class="loading"></div> Inapakia data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inventory Tab -->
        <div id="inventory" class="tab-content">
            <div class="content-card">
                <h3><i class="fas fa-box"></i> Ongeza Kifaa Kipya</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Jina la Kifaa</label>
                        <input type="text" id="itemName" class="form-control" placeholder="Mfano: Mkaa">
                    </div>
                    <div class="form-group">
                        <label>Idadi</label>
                        <input type="number" id="itemStock" class="form-control" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label>Kipimo</label>
                        <select id="itemUnit" class="form-control">
                            <option value="kg">Kg</option>
                            <option value="vipande">Vipande</option>
                            <option value="lita">Lita</option>
                            <option value="pakiti">Pakiti</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kiwango cha Chini</label>
                        <input type="number" id="itemMinStock" class="form-control" placeholder="10">
                    </div>
                </div>
                <button class="btn btn-primary" onclick="addInventoryItem()">
                    <i class="fas fa-plus"></i> Ongeza Kifaa
                </button>
            </div>

            <div class="content-card">
                <h3><i class="fas fa-boxes"></i> Orodha ya Vifaa</h3>
                <table class="table" id="inventoryTable">
                    <thead>
                        <tr>
                            <th>Kifaa</th>
                            <th>Stock</th>
                            <th>Kipimo</th>
                            <th>Kiwango cha Chini</th>
                            <th>Hali</th>
                            <th>Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody">
                        <tr><td colspan="6"><div class="loading"></div> Inapakia data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payments Tab -->
        <div id="payments" class="tab-content">
            <div class="content-card">
                <h3><i class="fas fa-money-bill-wave"></i> Historia ya Malipo</h3>
                <table class="table" id="paymentsTable">
                    <thead>
                        <tr>
                            <th>Mteja</th>
                            <th>Kiasi</th>
                            <th>Njia</th>
                            <th>Muda</th>
                            <th>Tarehe</th>
                        </tr>
                    </thead>
                    <tbody id="paymentsTableBody">
                        <tr><td colspan="5"><div class="loading"></div> Inapakia data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Employees Tab -->
        <div id="employees" class="tab-content">
            <div class="content-card">
                <h3><i class="fas fa-user-plus"></i> Ongeza Mfanyakazi</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Jina la Mfanyakazi</label>
                        <input type="text" id="employeeName" class="form-control" placeholder="Ingiza jina">
                    </div>
                    <div class="form-group">
                        <label>Nafasi</label>
                        <select id="employeePosition" class="form-control">
                            <option value="Waiter">Waiter</option>
                            <option value="Senior Waiter">Senior Waiter</option>
                            <option value="Cashier">Cashier</option>
                            <option value="Manager">Manager</option>
                            <option value="Security">Security</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Zamu</label>
                        <select id="employeeShift" class="form-control">
                            <option value="Mchana">Mchana (8AM - 4PM)</option>
                            <option value="Jioni">Jioni (4PM - 12AM)</option>
                            <option value="Usiku">Usiku (12AM - 8AM)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Mshahara (TZS)</label>
                        <input type="number" id="employeeSalary" class="form-control" placeholder="0">
                    </div>
                </div>
                <button class="btn btn-primary" onclick="addEmployee()">
                    <i class="fas fa-plus"></i> Ongeza Mfanyakazi
                </button>
            </div>

            <div class="content-card">
                <h3><i class="fas fa-user-friends"></i> Orodha ya Wafanyakazi</h3>
                <table class="table" id="employeesTable">
                    <thead>
                        <tr>
                            <th>Jina</th>
                            <th>Nafasi</th>
                            <th>Zamu</th>
                            <th>Huduma</th>
                            <th>Mshahara</th>
                            <th>Vitendo</th>
                        </tr>
                    </thead>
                    <tbody id="employeesTableBody">
                        <tr><td colspan="6"><div class="loading"></div> Inapakia data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reports Tab -->
        <div id="reports" class="tab-content">
            <div class="content-card">
                <h3><i class="fas fa-chart-bar"></i> Ripoti za Biashara</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Aina ya Ripoti</label>
                        <select id="reportType" class="form-control">
                            <option value="daily">Ripoti ya Kila Siku</option>
                            <option value="weekly">Ripoti ya Wiki</option>
                            <option value="monthly">Ripoti ya Mwezi</option>
                            <option value="flavors">Ripoti ya Flavors</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tarehe ya Kuanza</label>
                        <input type="date" id="startDate" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Tarehe ya Mwisho</label>
                        <input type="date" id="endDate" class="form-control">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" onclick="generateReport()">
                            <i class="fas fa-chart-line"></i> Tengeneza Ripoti
                        </button>
                    </div>
                </div>
                <div id="reportResults"></div>
            </div>
        </div>
    </div>

    <script>
        // Configuration - Change this to your server URL
        const API_BASE_URL = './'; // Current directory for localhost

        // Data cache
        let dataCache = {
            customers: [],
            flavors: [],
            inventory: [],
            payments: [],
            employees: []
        };

        // API Helper Function
        async function apiCall(endpoint, method = 'GET', data = null) {
            const config = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                }
            };
            
            if (data && method === 'POST') {
                config.body = JSON.stringify(data);
            }
            
            try {
                const response = await fetch(`${API_BASE_URL}${endpoint}`, config);
                const result = await response.json();
                return result;
            } catch (error) {
                console.error('API Error:', error);
                showAlert('Tatizo la muunganisho na server!', 'warning');
                return null;
            }
        }

        // Load data from server
        async function loadDataFromServer() {
            try {
                const [customers, flavors, inventory, payments, employees] = await Promise.all([
                    apiCall('api.php?action=get_customers'),
                    apiCall('api.php?action=get_flavors'),
                    apiCall('api.php?action=get_inventory'),
                    apiCall('api.php?action=get_payments'),
                    apiCall('api.php?action=get_employees')
                ]);
                
                if (customers && Array.isArray(customers)) dataCache.customers = customers;
                if (flavors && Array.isArray(flavors)) dataCache.flavors = flavors;
                if (inventory && Array.isArray(inventory)) dataCache.inventory = inventory;
                if (payments && Array.isArray(payments)) dataCache.payments = payments;
                if (employees && Array.isArray(employees)) dataCache.employees = employees;
                
                console.log('Data loaded from server successfully');
                return true;
            } catch (error) {
                console.error('Error loading data:', error);
                showAlert('Imeshindikana kupakia data kutoka server', 'warning');
                return false;
            }
        }

        // Tab Navigation
        function showTab(tabName) {
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));

            const navTabs = document.querySelectorAll('.nav-tab');
            navTabs.forEach(tab => tab.classList.remove('active'));

            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');

            switch(tabName) {
                case 'dashboard':
                    loadDashboard();
                    break;
                case 'customers':
                    loadCustomers();
                    break;
                case 'flavors':
                    loadFlavors();
                    break;
                case 'inventory':
                    loadInventory();
                    break;
                case 'payments':
                    loadPayments();
                    break;
                case 'employees':
                    loadEmployees();
                    break;
            }
        }

        // Dashboard Functions
        async function loadDashboard() {
            await updateStats();
            await showAlerts();
            loadRecentActivity();
        }

        async function updateStats() {
            try {
                const stats = await apiCall('api.php?action=get_daily_stats');
                if (stats) {
                    document.getElementById('todayCustomers').textContent = stats.total_customers || 0;
                    document.getElementById('todayRevenue').textContent = `TZS ${parseInt(stats.daily_revenue || 0).toLocaleString()}`;
                }
            } catch (error) {
                console.error('Error updating stats:', error);
            }
            
            // Calculate charcoal used (example calculation)
            const charcoalItem = dataCache.inventory.find(item => 
                item.item_name && (item.item_name.includes('Charcoal') || item.item_name.includes('Mkaa'))
            );
            if (charcoalItem) {
                const usedCharcoal = Math.max(0, 50 - charcoalItem.stock);
                document.getElementById('charcoalUsed').textContent = `${usedCharcoal} kg`;
            }
            
            const activeEmployees = dataCache.employees.filter(emp => emp.status === 'active' || !emp.status).length;
            document.getElementById('activeEmployees').textContent = activeEmployees;
        }

        async function showAlerts() {
            const alertsDiv = document.getElementById('alerts');
            
            try {
                const lowStockFlavors = await apiCall('api.php?action=get_low_stock_alerts');
                const lowStockInventory = dataCache.inventory.filter(i => i.stock <= i.min_stock);

                if ((lowStockFlavors && lowStockFlavors.length > 0) || lowStockInventory.length > 0) {
                    let alertHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> <strong>Tahadhari ya Stock!</strong><br>';
                    
                    if (lowStockFlavors && lowStockFlavors.length > 0) {
                        alertHTML += `Flavors zinazoisha: ${lowStockFlavors.map(f => f.name).join(', ')}<br>`;
                    }
                    
                    if (lowStockInventory.length > 0) {
                        alertHTML += `Vifaa vinavyoisha: ${lowStockInventory.map(i => i.item_name).join(', ')}`;
                    }
                    
                    alertHTML += '</div>';
                    alertsDiv.innerHTML = alertHTML;
                } else {
                    alertsDiv.innerHTML = '';
                }
            } catch (error) {
                alertsDiv.innerHTML = '';
            }
        }

        function loadRecentActivity() {
            const activityDiv = document.getElementById('recentActivity');
            const recentPayments = dataCache.payments.slice(-5).reverse();
            
            if (recentPayments.length === 0) {
                activityDiv.innerHTML = '<p>Hakuna shughuli za hivi karibuni</p>';
                return;
            }
            
            let html = '<table class="table"><thead><tr><th>Mteja</th><th>Kiasi</th><th>Njia</th><th>Muda</th></tr></thead><tbody>';
            
            recentPayments.forEach(payment => {
                html += `
                    <tr>
                        <td>${payment.customer_name}</td>
                        <td>TZS ${parseInt(payment.amount).toLocaleString()}</td>
                        <td><span class="badge badge-primary">${payment.payment_method}</span></td>
                        <td>${payment.transaction_time}</td>
                    </tr>
                `;
            });
            
            html += '</tbody></table>';
            activityDiv.innerHTML = html;
        }

        // Customer Functions
        function loadCustomers() {
            const tbody = document.getElementById('customersTableBody');
            
            if (dataCache.customers.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6">Hakuna wateja bado</td></tr>';
                return;
            }
            
            let html = '';
            dataCache.customers.forEach(customer => {
                html += `
                    <tr>
                        <td>${customer.name}</td>
                        <td>${customer.visits}</td>
                        <td>${customer.last_visit}</td>
                        <td>${customer.favorite_flavor || 'N/A'}</td>
                        <td>TZS ${parseInt(customer.total_spent || 0).toLocaleString()}</td>
                        <td><span class="badge badge-success">${customer.status}</span></td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
            
            // Populate flavor dropdown
            populateFlavorDropdown();
        }

        function populateFlavorDropdown() {
            const flavorSelect = document.getElementById('customerFlavor');
            flavorSelect.innerHTML = '<option value="">Chagua Flavor</option>';
            
            dataCache.flavors.forEach(flavor => {
                const option = document.createElement('option');
                option.value = flavor.name;
                option.textContent = `${flavor.name} - TZS ${parseInt(flavor.price).toLocaleString()}`;
                flavorSelect.appendChild(option);
            });
        }

        async function addCustomer() {
            const name = document.getElementById('customerName').value.trim();
            const flavor = document.getElementById('customerFlavor').value;
            const amount = parseInt(document.getElementById('customerAmount').value);
            const paymentMethod = document.getElementById('paymentMethod').value;

            if (!name || !flavor || !amount) {
                showAlert('Tafadhali jaza taarifa zote!', 'warning');
                return;
            }

            const customerData = {
                action: 'add_customer',
                name: name,
                flavor: flavor,
                amount: amount,
                payment_method: paymentMethod
            };

            const result = await apiCall('api.php', 'POST', customerData);
            
            if (result && result.message && result.message.includes('kikamilifu')) {
                // Clear form
                document.getElementById('customerName').value = '';
                document.getElementById('customerFlavor').value = '';
                document.getElementById('customerAmount').value = '';
                document.getElementById('paymentMethod').value = 'Cash';

                // Reload data
                await loadDataFromServer();
                loadCustomers();
                if (document.querySelector('.tab-content.active').id === 'dashboard') {
                    loadDashboard();
                }
                showAlert('Mteja ameongezwa kikamilifu!', 'success');
            } else {
                showAlert('Hitilafu imetokea wakati wa kuongeza mteja!', 'warning');
            }
        }

        // Flavor Functions
        function loadFlavors() {
            const tbody = document.getElementById('flavorsTableBody');
            
            if (dataCache.flavors.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6">Hakuna flavors bado</td></tr>';
                return;
            }
            
            let html = '';
            dataCache.flavors.forEach(flavor => {
                const status = flavor.stock <= flavor.min_stock ? 'warning' : 'success';
                const statusText = flavor.stock <= flavor.min_stock ? 'Chini' : 'Tosha';
                
                html += `
                    <tr>
                        <td>${flavor.name}</td>
                        <td>${flavor.stock}</td>
                        <td>TZS ${parseInt(flavor.price).toLocaleString()}</td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: ${flavor.popularity}%"></div>
                            </div>
                            ${flavor.popularity}%
                        </td>
                        <td><span class="badge badge-${status}">${statusText}</span></td>
                        <td>
                            <button class="btn btn-success" onclick="updateFlavorStock(${flavor.id}, ${flavor.stock + 5})" title="Ongeza 5">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button class="btn btn-danger" onclick="updateFlavorStock(${flavor.id}, ${Math.max(0, flavor.stock - 5)})" title="Punguza 5">
                                <i class="fas fa-minus"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }

        async function addFlavor() {
            const name = document.getElementById('flavorName').value.trim();
            const stock = parseInt(document.getElementById('flavorStock').value);
            const price = parseInt(document.getElementById('flavorPrice').value);
            const minStock = parseInt(document.getElementById('flavorMinStock').value);

            if (!name || !stock || !price || !minStock) {
                showAlert('Tafadhali jaza taarifa zote!', 'warning');
                return;
            }

            const flavorData = {
                action: 'add_flavor',
                name: name,
                stock: stock,
                price: price,
                min_stock: minStock
            };

            const result = await apiCall('api.php', 'POST', flavorData);
            
            if (result && result.message && result.message.includes('kikamilifu')) {
                // Clear form
                document.getElementById('flavorName').value = '';
                document.getElementById('flavorStock').value = '';
                document.getElementById('flavorPrice').value = '';
                document.getElementById('flavorMinStock').value = '';

                // Reload data
                await loadDataFromServer();
                loadFlavors();
                showAlert('Flavor imeongezwa kikamilifu!', 'success');
            } else {
                showAlert('Hitilafu imetokea wakati wa kuongeza flavor!', 'warning');
            }
        }

        async function updateFlavorStock(flavorId, newStock) {
            const updateData = {
                action: 'update_flavor_stock',
                id: flavorId,
                stock: newStock
            };

            const result = await apiCall('api.php', 'POST', updateData);
            
            if (result && result.message && result.message.includes('badilishwa')) {
                await loadDataFromServer();
                loadFlavors();
                showAlert('Stock ya flavor imebadilishwa!', 'success');
            } else {
                showAlert('Hitilafu imetokea wakati wa kubadilisha stock!', 'warning');
            }
        }

        // Inventory Functions
        function loadInventory() {
            const tbody = document.getElementById('inventoryTableBody');
            
            if (dataCache.inventory.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6">Hakuna vifaa bado</td></tr>';
                return;
            }
            
            let html = '';
            dataCache.inventory.forEach(item => {
                const status = item.stock <= item.min_stock ? 'warning' : 'success';
                const statusText = item.stock <= item.min_stock ? 'Chini' : 'Tosha';
                
                html += `
                    <tr>
                        <td>${item.item_name}</td>
                        <td>${item.stock}</td>
                        <td>${item.unit}</td>
                        <td>${item.min_stock}</td>
                        <td><span class="badge badge-${status}">${statusText}</span></td>
                        <td>
                            <button class="btn btn-success" onclick="updateInventoryStock(${item.id}, ${item.stock + 10})" title="Ongeza 10">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button class="btn btn-danger" onclick="updateInventoryStock(${item.id}, ${Math.max(0, item.stock - 10)})" title="Punguza 10">
                                <i class="fas fa-minus"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }

        async function addInventoryItem() {
            const name = document.getElementById('itemName').value.trim();
            const stock = parseInt(document.getElementById('itemStock').value);
            const unit = document.getElementById('itemUnit').value;
            const minStock = parseInt(document.getElementById('itemMinStock').value);

            if (!name || !stock || !unit || !minStock) {
                showAlert('Tafadhali jaza taarifa zote!', 'warning');
                return;
            }

            const itemData = {
                action: 'add_inventory',
                item_name: name,
                stock: stock,
                unit: unit,
                min_stock: minStock
            };

            const result = await apiCall('api.php', 'POST', itemData);
            
            if (result && result.message && result.message.includes('kikamilifu')) {
                // Clear form
                document.getElementById('itemName').value = '';
                document.getElementById('itemStock').value = '';
                document.getElementById('itemUnit').value = 'kg';
                document.getElementById('itemMinStock').value = '';

                // Reload data
                await loadDataFromServer();
                loadInventory();
                showAlert('Kifaa kimeongezwa kikamilifu!', 'success');
            } else {
                showAlert('Hitilafu imetokea wakati wa kuongeza kifaa!', 'warning');
            }
        }

        async function updateInventoryStock(itemId, newStock) {
            const updateData = {
                action: 'update_inventory_stock',
                id: itemId,
                stock: newStock
            };

            const result = await apiCall('api.php', 'POST', updateData);
            
            if (result && result.message && result.message.includes('badilishwa')) {
                await loadDataFromServer();
                loadInventory();
                showAlert('Stock ya kifaa imebadilishwa!', 'success');
            } else {
                showAlert('Hitilafu imetokea wakati wa kubadilisha stock!', 'warning');
            }
        }

        // Payment Functions
        function loadPayments() {
            const tbody = document.getElementById('paymentsTableBody');
            
            if (dataCache.payments.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5">Hakuna malipo bado</td></tr>';
                return;
            }
            
            let html = '';
            dataCache.payments.forEach(payment => {
                html += `
                    <tr>
                        <td>${payment.customer_name}</td>
                        <td>TZS ${parseInt(payment.amount).toLocaleString()}</td>
                        <td><span class="badge badge-primary">${payment.payment_method}</span></td>
                        <td>${payment.transaction_time}</td>
                        <td>${payment.transaction_date}</td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }

        // Employee Functions
        function loadEmployees() {
            const tbody = document.getElementById('employeesTableBody');
            
            if (dataCache.employees.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6">Hakuna wafanyakazi bado</td></tr>';
                return;
            }
            
            let html = '';
            dataCache.employees.forEach(employee => {
                html += `
                    <tr>
                        <td>${employee.name}</td>
                        <td>${employee.position}</td>
                        <td>${employee.shift}</td>
                        <td>${employee.services_count}</td>
                        <td>TZS ${parseInt(employee.salary).toLocaleString()}</td>
                        <td>
                            <button class="btn btn-primary" onclick="editEmployee(${employee.id})" title="Hariri mshahara">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }

        async function addEmployee() {
            const name = document.getElementById('employeeName').value.trim();
            const position = document.getElementById('employeePosition').value;
            const shift = document.getElementById('employeeShift').value;
            const salary = parseInt(document.getElementById('employeeSalary').value);

            if (!name || !position || !shift || !salary) {
                showAlert('Tafadhali jaza taarifa zote!', 'warning');
                return;
            }

            const employeeData = {
                action: 'add_employee',
                name: name,
                position: position,
                shift: shift,
                salary: salary
            };

            const result = await apiCall('api.php', 'POST', employeeData);
            
            if (result && result.message && result.message.includes('kikamilifu')) {
                // Clear form
                document.getElementById('employeeName').value = '';
                document.getElementById('employeePosition').value = 'Waiter';
                document.getElementById('employeeShift').value = 'Mchana';
                document.getElementById('employeeSalary').value = '';

                // Reload data
                await loadDataFromServer();
                loadEmployees();
                showAlert('Mfanyakazi ameongezwa kikamilifu!', 'success');
            } else {
                showAlert('Hitilafu imetokea wakati wa kuongeza mfanyakazi!', 'warning');
            }
        }

        async function editEmployee(id) {
            const employee = dataCache.employees.find(e => e.id === id);
            if (employee) {
                const newSalary = prompt(`Ingiza mshahara mpya kwa ${employee.name}:`, employee.salary);
                if (newSalary && !isNaN(newSalary)) {
                    const updateData = {
                        action: 'update_employee_salary',
                        id: id,
                        salary: parseInt(newSalary)
                    };

                    const result = await apiCall('api.php', 'POST', updateData);
                    
                    if (result && result.message && result.message.includes('badilishwa')) {
                        await loadDataFromServer();
                        loadEmployees();
                        showAlert('Taarifa za mfanyakazi zimebadilishwa!', 'success');
                    } else {
                        showAlert('Hitilafu imetokea wakati wa kubadilisha taarifa!', 'warning');
                    }
                }
            }
        }

       // Report Functions
        async function generateReport() {
            const reportType = document.getElementById('reportType').value;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const resultsDiv = document.getElementById('reportResults');

            resultsDiv.innerHTML = '<div class="loading"></div> Inatengeneza ripoti...';

            let reportHTML = '<div class="content-card"><h4>Matokeo ya Ripoti</h4>';

            try {
                let reportData;
                
                switch(reportType) {
                    case 'daily':
                        reportData = await apiCall(`reports.php?report_type=daily&date=${startDate}`);
                        if (reportData && !reportData.error) {
                            reportHTML += `
                                <h5>📊 Ripoti ya Siku: ${reportData.date}</h5>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0;">
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Mapato</h6>
                                        <h3 style="color: #28a745;">TZS ${parseInt(reportData.revenue.total_revenue || 0).toLocaleString()}</h3>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Malipo</h6>
                                        <h3 style="color: #007bff;">${reportData.revenue.transactions || 0}</h3>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Wastani</h6>
                                        <h3 style="color: #6c757d;">TZS ${parseInt(reportData.revenue.average_transaction || 0).toLocaleString()}</h3>
                                    </div>
                                </div>
                            `;
                            
                            if (reportData.top_flavors && reportData.top_flavors.length > 0) {
                                reportHTML += '<h6>🔥 Flavors Zinazopendwa Zaidi:</h6><table class="table"><thead><tr><th>Flavor</th><th>Orders</th><th>Mapato</th></tr></thead><tbody>';
                                reportData.top_flavors.forEach(flavor => {
                                    reportHTML += `<tr><td>${flavor.name}</td><td>${flavor.orders}</td><td>TZS ${parseInt(flavor.revenue).toLocaleString()}</td></tr>`;
                                });
                                reportHTML += '</tbody></table>';
                            }
                            
                            if (reportData.payment_methods && reportData.payment_methods.length > 0) {
                                reportHTML += '<h6>💳 Njia za Malipo:</h6><table class="table"><thead><tr><th>Njia</th><th>Idadi</th><th>Kiasi</th></tr></thead><tbody>';
                                reportData.payment_methods.forEach(method => {
                                    reportHTML += `<tr><td>${method.payment_method}</td><td>${method.count}</td><td>TZS ${parseInt(method.total).toLocaleString()}</td></tr>`;
                                });
                                reportHTML += '</tbody></table>';
                            }
                        }
                        break;

                    case 'weekly':
                        reportData = await apiCall(`reports.php?report_type=weekly&date=${startDate}`);
                        if (reportData && !reportData.error) {
                            reportHTML += `
                                <h5>📅 Ripoti ya Wiki: ${reportData.period}</h5>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin: 20px 0;">
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Jumla Mapato</h6>
                                        <h3 style="color: #28a745;">TZS ${parseInt(reportData.totals.total_revenue || 0).toLocaleString()}</h3>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Malipo</h6>
                                        <h3 style="color: #007bff;">${reportData.totals.total_transactions || 0}</h3>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Wateja</h6>
                                        <h3 style="color: #6c757d;">${reportData.totals.unique_customers || 0}</h3>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Wastani</h6>
                                        <h3 style="color: #6c757d;">TZS ${parseInt(reportData.totals.average_transaction || 0).toLocaleString()}</h3>
                                    </div>
                                </div>
                            `;
                            
                            if (reportData.daily_breakdown && reportData.daily_breakdown.length > 0) {
                                reportHTML += '<h6>📈 Breakdown ya Kila Siku:</h6><table class="table"><thead><tr><th>Tarehe</th><th>Mapato</th><th>Malipo</th></tr></thead><tbody>';
                                reportData.daily_breakdown.forEach(day => {
                                    reportHTML += `<tr><td>${day.transaction_date}</td><td>TZS ${parseInt(day.daily_revenue).toLocaleString()}</td><td>${day.daily_transactions}</td></tr>`;
                                });
                                reportHTML += '</tbody></table>';
                            }
                        }
                        break;

                    case 'monthly':
                        const month = new Date(startDate).getMonth() + 1;
                        const year = new Date(startDate).getFullYear();
                        reportData = await apiCall(`reports.php?report_type=monthly&month=${month}&year=${year}`);
                        
                        if (reportData && !reportData.error) {
                            reportHTML += `
                                <h5>📅 Ripoti ya Mwezi: ${reportData.month_name}</h5>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin: 20px 0;">
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Mapato ya Mwezi</h6>
                                        <h3 style="color: #28a745;">TZS ${parseInt(reportData.totals.total_revenue || 0).toLocaleString()}</h3>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Jumla Malipo</h6>
                                        <h3 style="color: #007bff;">${reportData.totals.total_transactions || 0}</h3>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Wateja wa Kipekee</h6>
                                        <h3 style="color: #6c757d;">${reportData.totals.unique_customers || 0}</h3>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Wastani wa Malipo</h6>
                                        <h3 style="color: #6c757d;">TZS ${parseInt(reportData.totals.average_transaction || 0).toLocaleString()}</h3>
                                    </div>
                                </div>
                            `;
                            
                            if (reportData.top_customers && reportData.top_customers.length > 0) {
                                reportHTML += '<h6>⭐ Wateja Wakuu wa Mwezi:</h6><table class="table"><thead><tr><th>Jina</th><th>Ziara</th><th>Jumla Ametumia</th></tr></thead><tbody>';
                                reportData.top_customers.slice(0, 10).forEach(customer => {
                                    reportHTML += `<tr><td>${customer.customer_name}</td><td>${customer.visits}</td><td>TZS ${parseInt(customer.total_spent).toLocaleString()}</td></tr>`;
                                });
                                reportHTML += '</tbody></table>';
                            }
                        }
                        break;

                    case 'flavors':
                        reportData = await apiCall(`reports.php?report_type=flavors`);
                        if (reportData && !reportData.error) {
                            reportHTML += `
                                <h5>🚬 Uchambuzi wa Flavors</h5>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin: 20px 0;">
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Jumla Flavors</h6>
                                        <h3 style="color: #007bff;">${reportData.total_flavors || 0}</h3>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Stock Chini</h6>
                                        <h3 style="color: #dc3545;">${reportData.low_stock_count || 0}</h3>
                                    </div>
                                </div>
                            `;
                            
                            if (reportData.flavors && reportData.flavors.length > 0) {
                                reportHTML += '<h6>📊 Orodha ya Flavors:</h6><table class="table"><thead><tr><th>Jina</th><th>Stock</th><th>Bei</th><th>Umaarufu</th><th>Hali</th></tr></thead><tbody>';
                                reportData.flavors.forEach(flavor => {
                                    const statusColor = flavor.stock_status === 'Low Stock' ? '#dc3545' : 
                                                       flavor.stock_status === 'Medium Stock' ? '#ffc107' : '#28a745';
                                    reportHTML += `<tr>
                                        <td>${flavor.name}</td>
                                        <td>${flavor.stock}</td>
                                        <td>TZS ${parseInt(flavor.price).toLocaleString()}</td>
                                        <td>${flavor.popularity}%</td>
                                        <td><span style="color: ${statusColor}; font-weight: bold;">${flavor.stock_status}</span></td>
                                    </tr>`;
                                });
                                reportHTML += '</tbody></table>';
                            }
                            
                            if (reportData.low_stock_alerts && reportData.low_stock_alerts.length > 0) {
                                reportHTML += '<h6 style="color: #dc3545;">⚠️ Stock Alerts:</h6><table class="table"><thead><tr><th>Flavor</th><th>Stock Current</th><th>Stock Minimum</th></tr></thead><tbody>';
                                reportData.low_stock_alerts.forEach(alert => {
                                    reportHTML += `<tr style="background: #fff3cd;"><td>${alert.name}</td><td style="color: #dc3545; font-weight: bold;">${alert.stock}</td><td>${alert.min_stock}</td></tr>`;
                                });
                                reportHTML += '</tbody></table>';
                            }
                            
                            if (reportData.flavor_sales && reportData.flavor_sales.length > 0) {
                                reportHTML += '<h6>💰 Mauzo ya Flavors:</h6><table class="table"><thead><tr><th>Flavor</th><th>Wateja</th><th>Mapato</th></tr></thead><tbody>';
                                reportData.flavor_sales.forEach(sale => {
                                    reportHTML += `<tr><td>${sale.flavor}</td><td>${sale.customer_count}</td><td>TZS ${parseInt(sale.total_revenue).toLocaleString()}</td></tr>`;
                                });
                                reportHTML += '</tbody></table>';
                            }
                        }
                        break;

                    default:
                        reportHTML += '<p>Aina ya ripoti haijulikani.</p>';
                }
                
                if (!reportData || reportData.error) {
                    reportHTML += `<p style="color: red;">Hitilafu: ${reportData?.message || 'Haiwezi kupakia data'}</p>`;
                }
                
            } catch (error) {
                console.error('Report generation error:', error);
                reportHTML += '<p style="color: red;">Hitilafu imetokea wakati wa kutengeneza ripoti. Jaribu tena.</p>';
            }

            reportHTML += '</div>';
            resultsDiv.innerHTML = reportHTML;
        }

        // Enhanced report functions - add more report types
        async function generateAdvancedReport(type) {
            const resultsDiv = document.getElementById('reportResults');
            resultsDiv.innerHTML = '<div class="loading"></div> Inatengeneza ripoti ya kina...';

            try {
                let reportData;
                let reportHTML = '<div class="content-card">';

                switch(type) {
                    case 'inventory':
                        reportData = await apiCall(`reports.php?report_type=inventory`);
                        if (reportData && !reportData.error) {
                            reportHTML += `
                                <h4>📦 Ripoti ya Inventory</h4>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin: 20px 0;">
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Jumla Vifaa</h6>
                                        <h3 style="color: #007bff;">${reportData.summary.total_items}</h3>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Critical</h6>
                                        <h3 style="color: #dc3545;">${reportData.summary.critical_items}</h3>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Low Stock</h6>
                                        <h3 style="color: #ffc107;">${reportData.summary.low_stock_items}</h3>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Good Stock</h6>
                                        <h3 style="color: #28a745;">${reportData.summary.good_stock_items}</h3>
                                    </div>
                                </div>
                            `;
                            
                            if (reportData.inventory && reportData.inventory.length > 0) {
                                reportHTML += '<table class="table"><thead><tr><th>Kifaa</th><th>Stock</th><th>Unit</th><th>Min Stock</th><th>Hali</th><th>Last Updated</th></tr></thead><tbody>';
                                reportData.inventory.forEach(item => {
                                    const statusColor = item.status === 'Critical' ? '#dc3545' : 
                                                       item.status === 'Low' ? '#ffc107' : '#28a745';
                                    reportHTML += `<tr>
                                        <td>${item.item_name}</td>
                                        <td>${item.stock}</td>
                                        <td>${item.unit}</td>
                                        <td>${item.min_stock}</td>
                                        <td><span style="color: ${statusColor}; font-weight: bold;">${item.status}</span></td>
                                        <td>${item.last_updated}</td>
                                    </tr>`;
                                });
                                reportHTML += '</tbody></table>';
                            }
                        }
                        break;

                    case 'employees':
                        reportData = await apiCall(`reports.php?report_type=employees`);
                        if (reportData && !reportData.error) {
                            reportHTML += `
                                <h4>👥 Ripoti ya Wafanyakazi</h4>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin: 20px 0;">
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Jumla Wafanyakazi</h6>
                                        <h3 style="color: #007bff;">${reportData.summary.total_employees}</h3>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Mishahara ya Mwezi</h6>
                                        <h3 style="color: #28a745;">TZS ${parseInt(reportData.summary.total_monthly_salary).toLocaleString()}</h3>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Jumla Huduma</h6>
                                        <h3 style="color: #6c757d;">${reportData.summary.total_services_count}</h3>
                                    </div>
                                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h6>Wastani/Mtu</h6>
                                        <h3 style="color: #6c757d;">${reportData.summary.average_services_per_employee}</h3>
                                    </div>
                                </div>
                            `;
                            
                            if (reportData.employees && reportData.employees.length > 0) {
                                reportHTML += '<table class="table"><thead><tr><th>Jina</th><th>Nafasi</th><th>Zamu</th><th>Huduma</th><th>Mshahara</th><th>Wastani/Siku</th></tr></thead><tbody>';
                                reportData.employees.forEach(emp => {
                                    reportHTML += `<tr>
                                        <td>${emp.name}</td>
                                        <td>${emp.position}</td>
                                        <td>${emp.shift}</td>
                                        <td>${emp.services_count}</td>
                                        <td>TZS ${parseInt(emp.salary).toLocaleString()}</td>
                                        <td>${emp.daily_average}</td>
                                    </tr>`;
                                });
                                reportHTML += '</tbody></table>';
                            }
                        }
                        break;
                }

                reportHTML += '</div>';
                resultsDiv.innerHTML = reportHTML;

            } catch (error) {
                console.error('Advanced report error:', error);
                resultsDiv.innerHTML = '<div class="content-card"><p style="color: red;">Hitilafu imetokea wakati wa kutengeneza ripoti.</p></div>';
            }
        }

        // Add advanced report buttons to reports tab
        function addAdvancedReportButtons() {
            const reportsTab = document.getElementById('reports');
            const existingCard = reportsTab.querySelector('.content-card');
            
            if (existingCard && !document.getElementById('advancedReports')) {
                const advancedCard = document.createElement('div');
                advancedCard.className = 'content-card';
                advancedCard.id = 'advancedReports';
                advancedCard.innerHTML = `
                    <h3><i class="fas fa-chart-pie"></i> Ripoti za Kina</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <button class="btn btn-primary" onclick="generateAdvancedReport('inventory')">
                            <i class="fas fa-boxes"></i> Ripoti ya Inventory
                        </button>
                        <button class="btn btn-primary" onclick="generateAdvancedReport('employees')">
                            <i class="fas fa-users"></i> Ripoti ya Wafanyakazi
                        </button>
                        <button class="btn btn-primary" onclick="generateWeeklyReport()">
                            <i class="fas fa-calendar-week"></i> Ripoti ya Wiki
                        </button>
                        <button class="btn btn-primary" onclick="exportReportData()">
                            <i class="fas fa-download"></i> Export Data
                        </button>
                    </div>
                `;
                
                existingCard.parentNode.insertBefore(advancedCard, existingCard);
            }
        }

        // Generate weekly report
        async function generateWeeklyReport() {
            const today = new Date().toISOString().split('T')[0];
            const resultsDiv = document.getElementById('reportResults');
            resultsDiv.innerHTML = '<div class="loading"></div> Inatengeneza ripoti ya wiki...';

            try {
                const reportData = await apiCall(`reports.php?report_type=weekly&date=${today}`);
                
                if (reportData && !reportData.error) {
                    let reportHTML = `
                        <div class="content-card">
                            <h4>📅 Ripoti ya Wiki: ${reportData.period}</h4>
                            <p><strong>Kipindi:</strong> ${reportData.start_date} hadi ${reportData.end_date}</p>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin: 20px 0;">
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                    <h6>Jumla Mapato</h6>
                                    <h3 style="color: #28a745;">TZS ${parseInt(reportData.totals.total_revenue || 0).toLocaleString()}</h3>
                                </div>
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                    <h6>Malipo</h6>
                                    <h3 style="color: #007bff;">${reportData.totals.total_transactions || 0}</h3>
                                </div>
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                    <h6>Wateja</h6>
                                    <h3 style="color: #6c757d;">${reportData.totals.unique_customers || 0}</h3>
                                </div>
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                                    <h6>Wastani</h6>
                                    <h3 style="color: #6c757d;">TZS ${parseInt(reportData.totals.average_transaction || 0).toLocaleString()}</h3>
                                </div>
                            </div>
                    `;
                    
                    if (reportData.daily_breakdown && reportData.daily_breakdown.length > 0) {
                        reportHTML += '<h5>📊 Breakdown ya Kila Siku:</h5><table class="table"><thead><tr><th>Tarehe</th><th>Mapato</th><th>Malipo</th></tr></thead><tbody>';
                        reportData.daily_breakdown.forEach(day => {
                            reportHTML += `<tr><td>${day.transaction_date}</td><td>TZS ${parseInt(day.daily_revenue).toLocaleString()}</td><td>${day.daily_transactions}</td></tr>`;
                        });
                        reportHTML += '</tbody></table>';
                    }
                    
                    reportHTML += '</div>';
                    resultsDiv.innerHTML = reportHTML;
                } else {
                    resultsDiv.innerHTML = '<div class="content-card"><p style="color: red;">Haiwezi kupata data ya wiki.</p></div>';
                }
                
            } catch (error) {
                console.error('Weekly report error:', error);
                resultsDiv.innerHTML = '<div class="content-card"><p style="color: red;">Hitilafu imetokea wakati wa kutengeneza ripoti ya wiki.</p></div>';
            }
        }

        // Export report data
        async function exportReportData() {
            try {
                showAlert('Inaexport data...', 'success');
                
                const [customers, flavors, inventory, payments, employees] = await Promise.all([
                    apiCall('api.php?action=get_customers'),
                    apiCall('api.php?action=get_flavors'),
                    apiCall('api.php?action=get_inventory'),
                    apiCall('api.php?action=get_payments'),
                    apiCall('api.php?action=get_employees')
                ]);
                
                const exportData = {
                    export_date: new Date().toISOString(),
                    summary: {
                        total_customers: customers?.length || 0,
                        total_flavors: flavors?.length || 0,
                        total_inventory_items: inventory?.length || 0,
                        total_payments: payments?.length || 0,
                        total_employees: employees?.length || 0
                    },
                    data: {
                        customers: customers || [],
                        flavors: flavors || [],
                        inventory: inventory || [],
                        payments: payments || [],
                        employees: employees || []
                    }
                };
                
                const dataStr = JSON.stringify(exportData, null, 2);
                const dataBlob = new Blob([dataStr], {type: 'application/json'});
                
                const link = document.createElement('a');
                link.href = URL.createObjectURL(dataBlob);
                link.download = `shisha_lounge_export_${new Date().toISOString().split('T')[0]}.json`;
                link.click();
                
                showAlert('Data imeexport kikamilifu!', 'success');
                
            } catch (error) {
                console.error('Export error:', error);
                showAlert('Hitilafu imetokea wakati wa kuexport data!', 'warning');
            }
        }

        // Utility Functions
        function showAlert(message, type) {
            // Remove any existing alerts
            const existingAlert = document.querySelector('.floating-alert');
            if (existingAlert) {
                existingAlert.remove();
            }

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} floating-alert`;
            alertDiv.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                animation: slideIn 0.3s ease-out;
            `;
            
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            alertDiv.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.style.animation = 'slideOut 0.3s ease-in forwards';
                setTimeout(() => alertDiv.remove(), 300);
            }, 3000);
        }

        // Auto-refresh functionality
        let refreshInterval;

        function startAutoRefresh() {
            refreshInterval = setInterval(async () => {
                await loadDataFromServer();
                if (document.querySelector('.tab-content.active').id === 'dashboard') {
                    loadDashboard();
                }
            }, 30000); // Refresh every 30 seconds
        }

        function stopAutoRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        }

        // Initialize application
        document.addEventListener('DOMContentLoaded', async function() {
            // Show loading state
            console.log('Initializing Shisha Lounge Management System...');
            
            try {
                // Load initial data from server
                const success = await loadDataFromServer();
                
                if (success) {
                    // Initialize dashboard
                    await loadDashboard();
                    
                    // Set default dates for reports
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('startDate').value = today;
                    document.getElementById('endDate').value = today;
                    
                    // Add advanced report buttons
                    addAdvancedReportButtons();
                    
                    // Start auto-refresh
                    startAutoRefresh();
                    
                    showAlert('Mfumo umepakiwa kikamilifu!', 'success');
                } else {
                    showAlert('Hitilafu imetokea wakati wa kupakia mfumo. Tunajaribu kurekebisha...', 'warning');
                    // Try to load with fallback data
                    loadDashboard();
                    addAdvancedReportButtons();
                }
                
            } catch (error) {
                console.error('Initialization error:', error);
                showAlert('Hitilafu imetokea wakati wa kupakia mfumo!', 'warning');
                // Load with empty data
                loadDashboard();
                addAdvancedReportButtons();
            }

            
            
        });

        // Handle page unload
        window.addEventListener('beforeunload', function() {
            stopAutoRefresh();
        });

        // Error handling for fetch operations
        window.addEventListener('unhandledrejection', function(event) {
            console.error('Unhandled promise rejection:', event.reason);
            showAlert('Hitilafu ya mfumo imetokea!', 'warning');
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            // Ctrl + R to refresh
            if (event.ctrlKey && event.key === 'r') {
                event.preventDefault();
                loadDataFromServer().then(() => {
                    showAlert('Data imesasishwa!', 'success');
                    // Reload current tab
                    const activeTab = document.querySelector('.tab-content.active');
                    if (activeTab) {
                        const tabName = activeTab.id;
                        switch(tabName) {
                            case 'dashboard': loadDashboard(); break;
                            case 'customers': loadCustomers(); break;
                            case 'flavors': loadFlavors(); break;
                            case 'inventory': loadInventory(); break;
                            case 'payments': loadPayments(); break;
                            case 'employees': loadEmployees(); break;
                        }
                    }
                });
            }
        });


        // Logout function
function logout() {
    if (confirm('Je, una uhakika unataka kuondoka?')) {
        // Show loading state
        const logoutBtn = document.querySelector('.logout-btn');
        if (logoutBtn) {
            logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inaondoka...';
            logoutBtn.disabled = true;
        }
        
        // Stop auto-refresh
        stopAutoRefresh();
        
        // Redirect to logout page
        window.location.href = 'logout.php';
    }
}

// Add hover effect to logout button
document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('mouseenter', function() {
            this.style.background = 'rgba(255, 255, 255, 0.3)';
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
        });
        
        logoutBtn.addEventListener('mouseleave', function() {
            this.style.background = 'rgba(255, 255, 255, 0.2)';
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    }
});
    </script>
</body>
</html>