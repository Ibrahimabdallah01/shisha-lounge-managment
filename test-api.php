<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test - Shisha Lounge</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 50px auto; 
            padding: 20px; 
            background: #f5f5f5;
        }
        .test-result {
            background: white;
            margin: 10px 0;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .success { border-left-color: #28a745; }
        .error { border-left-color: #dc3545; }
        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        .btn:hover { background: #0056b3; }
        .json-output {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 3px;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <h1>🧪 API Test - Shisha Lounge</h1>
    
    <div>
        <button class="btn" onclick="testAPI('test')">Test Basic API</button>
        <button class="btn" onclick="testAPI('get_customers')">Test Get Customers</button>
        <button class="btn" onclick="testAPI('get_flavors')">Test Get Flavors</button>
        <button class="btn" onclick="testAPI('get_inventory')">Test Get Inventory</button>
        <button class="btn" onclick="testAPI('get_payments')">Test Get Payments</button>
        <button class="btn" onclick="testAPI('get_employees')">Test Get Employees</button>
        <button class="btn" onclick="testAPI('get_daily_stats')">Test Daily Stats</button>
        <button class="btn" onclick="clearResults()">Clear Results</button>
    </div>

    <div id="results"></div>

    <script>
        async function testAPI(action) {
            const resultsDiv = document.getElementById('results');
            
            // Add loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'test-result';
            loadingDiv.innerHTML = `<h3>Testing: ${action}</h3><p>🔄 Loading...</p>`;
            resultsDiv.appendChild(loadingDiv);
            
            try {
                const response = await fetch(`api.php?action=${action}`);
                const text = await response.text();
                
                // Remove loading indicator
                loadingDiv.remove();
                
                // Create result div
                const resultDiv = document.createElement('div');
                resultDiv.className = 'test-result';
                
                try {
                    // Try to parse as JSON
                    const data = JSON.parse(text);
                    resultDiv.className += ' success';
                    resultDiv.innerHTML = `
                        <h3>✅ ${action} - SUCCESS</h3>
                        <p><strong>Status:</strong> ${response.status}</p>
                        <p><strong>Response:</strong></p>
                        <div class="json-output">${JSON.stringify(data, null, 2)}</div>
                    `;
                } catch (jsonError) {
                    resultDiv.className += ' error';
                    resultDiv.innerHTML = `
                        <h3>❌ ${action} - JSON PARSE ERROR</h3>
                        <p><strong>Status:</strong> ${response.status}</p>
                        <p><strong>JSON Error:</strong> ${jsonError.message}</p>
                        <p><strong>Raw Response:</strong></p>
                        <div class="json-output">${text}</div>
                    `;
                }
                
                resultsDiv.appendChild(resultDiv);
                
            } catch (error) {
                // Remove loading indicator
                loadingDiv.remove();
                
                const resultDiv = document.createElement('div');
                resultDiv.className = 'test-result error';
                resultDiv.innerHTML = `
                    <h3>❌ ${action} - NETWORK ERROR</h3>
                    <p><strong>Error:</strong> ${error.message}</p>
                `;
                resultsDiv.appendChild(resultDiv);
            }
        }

        function clearResults() {
            document.getElementById('results').innerHTML = '';
        }

        // Test adding a customer
        async function testAddCustomer() {
            const resultsDiv = document.getElementById('results');
            
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'test-result';
            loadingDiv.innerHTML = `<h3>Testing: Add Customer</h3><p>🔄 Adding test customer...</p>`;
            resultsDiv.appendChild(loadingDiv);
            
            try {
                const customerData = {
                    action: 'add_customer',
                    name: 'Test Customer ' + Date.now(),
                    flavor: 'Double Apple',
                    amount: 15000,
                    payment_method: 'Cash'
                };
                
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(customerData)
                });
                
                const text = await response.text();
                loadingDiv.remove();
                
                const resultDiv = document.createElement('div');
                resultDiv.className = 'test-result';
                
                try {
                    const data = JSON.parse(text);
                    resultDiv.className += ' success';
                    resultDiv.innerHTML = `
                        <h3>✅ Add Customer - SUCCESS</h3>
                        <p><strong>Response:</strong></p>
                        <div class="json-output">${JSON.stringify(data, null, 2)}</div>
                    `;
                } catch (jsonError) {
                    resultDiv.className += ' error';
                    resultDiv.innerHTML = `
                        <h3>❌ Add Customer - JSON PARSE ERROR</h3>
                        <p><strong>Raw Response:</strong></p>
                        <div class="json-output">${text}</div>
                    `;
                }
                
                resultsDiv.appendChild(resultDiv);
                
            } catch (error) {
                loadingDiv.remove();
                const resultDiv = document.createElement('div');
                resultDiv.className = 'test-result error';
                resultDiv.innerHTML = `
                    <h3>❌ Add Customer - ERROR</h3>
                    <p><strong>Error:</strong> ${error.message}</p>
                `;
                resultsDiv.appendChild(resultDiv);
            }
        }

        // Add test customer button
        document.addEventListener('DOMContentLoaded', function() {
            const btnContainer = document.querySelector('div');
            const testAddBtn = document.createElement('button');
            testAddBtn.className = 'btn';
            testAddBtn.textContent = 'Test Add Customer';
            testAddBtn.onclick = testAddCustomer;
            btnContainer.appendChild(testAddBtn);
        });
    </script>
</body>
</html>