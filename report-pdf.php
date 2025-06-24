<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Export Enhancement</title>
    <!-- Add jsPDF library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <style>
        .pdf-controls {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .export-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .export-card {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s ease;
            border: none;
        }
        
        .export-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .export-card i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }
        
        .export-card h4 {
            margin: 10px 0 5px 0;
            font-size: 1.1rem;
        }
        
        .export-card p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .btn-pdf {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-pdf:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }
        
        .pdf-preview {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .loading-pdf {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #e74c3c;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
    </style>
</head>
<body>
    <!-- PDF Export Controls Section -->
    <div class="pdf-controls">
        <h3><i class="fas fa-file-pdf" style="color: #e74c3c;"></i> PDF Export Center</h3>
        <p>Tengeneza ripoti za kibinafsi kwa format ya PDF kwa ajili ya presentation na backup.</p>
        
        <div class="export-grid">
            <button class="export-card" onclick="exportDailyReportPDF()">
                <i class="fas fa-calendar-day"></i>
                <h4>Daily Report</h4>
                <p>Ripoti ya siku moja kamili</p>
            </button>
            
            <button class="export-card" onclick="exportWeeklyReportPDF()">
                <i class="fas fa-calendar-week"></i>
                <h4>Weekly Report</h4>
                <p>Uchambuzi wa wiki nzima</p>
            </button>
            
            <button class="export-card" onclick="exportMonthlyReportPDF()">
                <i class="fas fa-calendar-alt"></i>
                <h4>Monthly Report</h4>
                <p>Ripoti ya mwezi mzima</p>
            </button>
            
            <button class="export-card" onclick="exportInventoryReportPDF()">
                <i class="fas fa-boxes"></i>
                <h4>Inventory Report</h4>
                <p>Hali ya stock na vifaa</p>
            </button>
            
            <button class="export-card" onclick="exportCustomerReportPDF()">
                <i class="fas fa-users"></i>
                <h4>Customer Report</h4>
                <p>Orodha ya wateja na stats</p>
            </button>
            
            <button class="export-card" onclick="exportFinancialReportPDF()">
                <i class="fas fa-chart-line"></i>
                <h4>Financial Report</h4>
                <p>Uchambuzi wa kifedha</p>
            </button>
        </div>
        
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
            <h4>Custom Date Range Export</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 15px; align-items: end;">
                <div>
                    <label>From Date:</label>
                    <input type="date" id="pdfStartDate" class="form-control">
                </div>
                <div>
                    <label>To Date:</label>
                    <input type="date" id="pdfEndDate" class="form-control">
                </div>
                <button class="btn-pdf" onclick="exportCustomRangePDF()">
                    <i class="fas fa-download"></i> Export Range
                </button>
            </div>
        </div>
    </div>

    <script>
        // PDF Export Functions
        const { jsPDF } = window.jspdf;

        // Utility function to format currency
        function formatCurrency(amount) {
            return `TZS ${parseInt(amount || 0).toLocaleString()}`;
        }

        // Utility function to get current date/time
        function getCurrentDateTime() {
            return new Date().toLocaleString('sw-TZ', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Add company header to PDF
        function addPDFHeader(doc, title) {
            // Company logo area (you can add actual logo later)
            doc.setFillColor(102, 126, 234);
            doc.rect(0, 0, 210, 30, 'F');
            
            // Company name
            doc.setTextColor(255, 255, 255);
            doc.setFontSize(20);
            doc.setFont(undefined, 'bold');
            doc.text('🚬 SHISHA LOUNGE MANAGEMENT', 20, 20);
            
            // Report title
            doc.setTextColor(0, 0, 0);
            doc.setFontSize(16);
            doc.setFont(undefined, 'bold');
            doc.text(title, 20, 45);
            
            // Generated date
            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text(`Generated: ${getCurrentDateTime()}`, 20, 55);
            
            return 65; // Return Y position for content start
        }

        // Add footer to PDF
        function addPDFFooter(doc) {
            const pageCount = doc.internal.getNumberOfPages();
            for (let i = 1; i <= pageCount; i++) {
                doc.setPage(i);
                doc.setFontSize(8);
                doc.setTextColor(128, 128, 128);
                doc.text(`Page ${i} of ${pageCount}`, 20, 290);
                doc.text('Shisha Lounge Management System', 150, 290);
            }
        }

        // Export Daily Report to PDF
        async function exportDailyReportPDF() {
            showAlert('Inatengeneza Daily Report PDF...', 'success');
            
            try {
                const today = new Date().toISOString().split('T')[0];
                const reportData = await apiCall(`reports.php?report_type=daily&date=${today}`);
                
                if (!reportData || reportData.error) {
                    showAlert('Hitilafu: Haiwezi kupata data ya ripoti', 'warning');
                    return;
                }

                const doc = new jsPDF();
                let yPos = addPDFHeader(doc, `DAILY REPORT - ${today}`);

                // Summary stats
                yPos += 10;
                doc.setFontSize(14);
                doc.setFont(undefined, 'bold');
                doc.text('MUHTASARI WA SIKU', 20, yPos);
                
                yPos += 15;
                doc.setFontSize(12);
                doc.setFont(undefined, 'normal');
                
                const stats = [
                    ['Mapato ya Siku:', formatCurrency(reportData.revenue?.total_revenue)],
                    ['Idadi ya Malipo:', reportData.revenue?.transactions || 0],
                    ['Wastani wa Malipo:', formatCurrency(reportData.revenue?.average_transaction)],
                    ['Tarehe:', today]
                ];

                stats.forEach(([label, value]) => {
                    doc.text(label, 20, yPos);
                    doc.setFont(undefined, 'bold');
                    doc.text(String(value), 80, yPos);
                    doc.setFont(undefined, 'normal');
                    yPos += 8;
                });

                // Top Flavors Table
                if (reportData.top_flavors && reportData.top_flavors.length > 0) {
                    yPos += 10;
                    doc.setFont(undefined, 'bold');
                    doc.text('FLAVORS ZINAZOPENDWA ZAIDI', 20, yPos);
                    yPos += 5;

                    const flavorData = reportData.top_flavors.map(flavor => [
                        flavor.name,
                        flavor.orders,
                        formatCurrency(flavor.revenue)
                    ]);

                    doc.autoTable({
                        startY: yPos,
                        head: [['Flavor', 'Orders', 'Mapato']],
                        body: flavorData,
                        theme: 'grid',
                        headStyles: { fillColor: [102, 126, 234] }
                    });
                    yPos = doc.lastAutoTable.finalY + 10;
                }

                // Payment Methods
                if (reportData.payment_methods && reportData.payment_methods.length > 0) {
                    doc.setFont(undefined, 'bold');
                    doc.text('NJIA ZA MALIPO', 20, yPos);
                    yPos += 5;

                    const paymentData = reportData.payment_methods.map(method => [
                        method.payment_method,
                        method.count,
                        formatCurrency(method.total)
                    ]);

                    doc.autoTable({
                        startY: yPos,
                        head: [['Njia ya Malipo', 'Idadi', 'Kiasi']],
                        body: paymentData,
                        theme: 'grid',
                        headStyles: { fillColor: [102, 126, 234] }
                    });
                }

                addPDFFooter(doc);
                doc.save(`Daily_Report_${today}.pdf`);
                showAlert('Daily Report PDF imetengenezwa!', 'success');

            } catch (error) {
                console.error('PDF Export Error:', error);
                showAlert('Hitilafu imetokea wakati wa kutengeneza PDF', 'warning');
            }
        }

        // Export Weekly Report to PDF
        async function exportWeeklyReportPDF() {
            showAlert('Inatengeneza Weekly Report PDF...', 'success');
            
            try {
                const today = new Date().toISOString().split('T')[0];
                const reportData = await apiCall(`reports.php?report_type=weekly&date=${today}`);
                
                if (!reportData || reportData.error) {
                    showAlert('Hitilafu: Haiwezi kupata data ya ripoti', 'warning');
                    return;
                }

                const doc = new jsPDF();
                let yPos = addPDFHeader(doc, `WEEKLY REPORT - ${reportData.period}`);

                // Summary stats
                yPos += 10;
                doc.setFontSize(14);
                doc.setFont(undefined, 'bold');
                doc.text('MUHTASARI WA WIKI', 20, yPos);
                
                yPos += 15;
                doc.setFontSize(12);
                doc.setFont(undefined, 'normal');
                
                const stats = [
                    ['Kipindi:', `${reportData.start_date} - ${reportData.end_date}`],
                    ['Jumla Mapato:', formatCurrency(reportData.totals?.total_revenue)],
                    ['Jumla Malipo:', reportData.totals?.total_transactions || 0],
                    ['Wateja wa Kipekee:', reportData.totals?.unique_customers || 0],
                    ['Wastani wa Malipo:', formatCurrency(reportData.totals?.average_transaction)]
                ];

                stats.forEach(([label, value]) => {
                    doc.text(label, 20, yPos);
                    doc.setFont(undefined, 'bold');
                    doc.text(String(value), 80, yPos);
                    doc.setFont(undefined, 'normal');
                    yPos += 8;
                });

                // Daily Breakdown Table
                if (reportData.daily_breakdown && reportData.daily_breakdown.length > 0) {
                    yPos += 10;
                    doc.setFont(undefined, 'bold');
                    doc.text('BREAKDOWN YA KILA SIKU', 20, yPos);
                    yPos += 5;

                    const dailyData = reportData.daily_breakdown.map(day => [
                        day.transaction_date,
                        formatCurrency(day.daily_revenue),
                        day.daily_transactions
                    ]);

                    doc.autoTable({
                        startY: yPos,
                        head: [['Tarehe', 'Mapato', 'Malipo']],
                        body: dailyData,
                        theme: 'grid',
                        headStyles: { fillColor: [102, 126, 234] }
                    });
                }

                addPDFFooter(doc);
                doc.save(`Weekly_Report_${today}.pdf`);
                showAlert('Weekly Report PDF imetengenezwa!', 'success');

            } catch (error) {
                console.error('PDF Export Error:', error);
                showAlert('Hitilafu imetokea wakati wa kutengeneza PDF', 'warning');
            }
        }

        // Export Monthly Report to PDF
        async function exportMonthlyReportPDF() {
            showAlert('Inatengeneza Monthly Report PDF...', 'success');
            
            try {
                const today = new Date();
                const month = today.getMonth() + 1;
                const year = today.getFullYear();
                const reportData = await apiCall(`reports.php?report_type=monthly&month=${month}&year=${year}`);
                
                if (!reportData || reportData.error) {
                    showAlert('Hitilafu: Haiwezi kupata data ya ripoti', 'warning');
                    return;
                }

                const doc = new jsPDF();
                let yPos = addPDFHeader(doc, `MONTHLY REPORT - ${reportData.month_name}`);

                // Summary stats
                yPos += 10;
                doc.setFontSize(14);
                doc.setFont(undefined, 'bold');
                doc.text('MUHTASARI WA MWEZI', 20, yPos);
                
                yPos += 15;
                doc.setFontSize(12);
                doc.setFont(undefined, 'normal');
                
                const stats = [
                    ['Mwezi:', reportData.month_name],
                    ['Mapato ya Mwezi:', formatCurrency(reportData.totals?.total_revenue)],
                    ['Jumla Malipo:', reportData.totals?.total_transactions || 0],
                    ['Wateja wa Kipekee:', reportData.totals?.unique_customers || 0],
                    ['Wastani wa Malipo:', formatCurrency(reportData.totals?.average_transaction)]
                ];

                stats.forEach(([label, value]) => {
                    doc.text(label, 20, yPos);
                    doc.setFont(undefined, 'bold');
                    doc.text(String(value), 90, yPos);
                    doc.setFont(undefined, 'normal');
                    yPos += 8;
                });

                // Top Customers Table
                if (reportData.top_customers && reportData.top_customers.length > 0) {
                    yPos += 10;
                    doc.setFont(undefined, 'bold');
                    doc.text('WATEJA WAKUU WA MWEZI', 20, yPos);
                    yPos += 5;

                    const customerData = reportData.top_customers.slice(0, 10).map(customer => [
                        customer.customer_name,
                        customer.visits,
                        formatCurrency(customer.total_spent)
                    ]);

                    doc.autoTable({
                        startY: yPos,
                        head: [['Jina la Mteja', 'Ziara', 'Jumla Ametumia']],
                        body: customerData,
                        theme: 'grid',
                        headStyles: { fillColor: [102, 126, 234] }
                    });
                }

                addPDFFooter(doc);
                doc.save(`Monthly_Report_${reportData.month_name.replace(' ', '_')}.pdf`);
                showAlert('Monthly Report PDF imetengenezwa!', 'success');

            } catch (error) {
                console.error('PDF Export Error:', error);
                showAlert('Hitilafu imetokea wakati wa kutengeneza PDF', 'warning');
            }
        }

        // Export Inventory Report to PDF
        async function exportInventoryReportPDF() {
            showAlert('Inatengeneza Inventory Report PDF...', 'success');
            
            try {
                const [inventoryData, flavorData] = await Promise.all([
                    apiCall('reports.php?report_type=inventory'),
                    apiCall('api.php?action=get_flavors')
                ]);
                
                if (!inventoryData || inventoryData.error) {
                    showAlert('Hitilafu: Haiwezi kupata data ya inventory', 'warning');
                    return;
                }

                const doc = new jsPDF();
                let yPos = addPDFHeader(doc, 'INVENTORY STATUS REPORT');

                // Summary stats
                yPos += 10;
                doc.setFontSize(14);
                doc.setFont(undefined, 'bold');
                doc.text('MUHTASARI WA INVENTORY', 20, yPos);
                
                yPos += 15;
                doc.setFontSize(12);
                doc.setFont(undefined, 'normal');
                
                const stats = [
                    ['Jumla Vifaa:', inventoryData.summary?.total_items || 0],
                    ['Critical Items:', inventoryData.summary?.critical_items || 0],
                    ['Low Stock Items:', inventoryData.summary?.low_stock_items || 0],
                    ['Good Stock Items:', inventoryData.summary?.good_stock_items || 0]
                ];

                stats.forEach(([label, value]) => {
                    doc.text(label, 20, yPos);
                    doc.setFont(undefined, 'bold');
                    doc.text(String(value), 80, yPos);
                    doc.setFont(undefined, 'normal');
                    yPos += 8;
                });

                // Inventory Table
                if (inventoryData.inventory && inventoryData.inventory.length > 0) {
                    yPos += 10;
                    doc.setFont(undefined, 'bold');
                    doc.text('HALI YA VIFAA', 20, yPos);
                    yPos += 5;

                    const inventoryTableData = inventoryData.inventory.map(item => [
                        item.item_name,
                        item.stock,
                        item.unit,
                        item.min_stock,
                        item.status
                    ]);

                    doc.autoTable({
                        startY: yPos,
                        head: [['Kifaa', 'Stock', 'Unit', 'Min Stock', 'Hali']],
                        body: inventoryTableData,
                        theme: 'grid',
                        headStyles: { fillColor: [102, 126, 234] }
                    });
                    yPos = doc.lastAutoTable.finalY + 10;
                }

                // Flavors Stock Table
                if (flavorData && flavorData.length > 0) {
                    if (yPos > 250) {
                        doc.addPage();
                        yPos = 20;
                    }
                    
                    doc.setFont(undefined, 'bold');
                    doc.text('HALI YA FLAVORS', 20, yPos);
                    yPos += 5;

                    const flavorTableData = flavorData.map(flavor => [
                        flavor.name,
                        flavor.stock,
                        flavor.min_stock,
                        formatCurrency(flavor.price),
                        flavor.stock <= flavor.min_stock ? 'Chini' : 'Tosha'
                    ]);

                    doc.autoTable({
                        startY: yPos,
                        head: [['Flavor', 'Stock', 'Min Stock', 'Bei', 'Hali']],
                        body: flavorTableData,
                        theme: 'grid',
                        headStyles: { fillColor: [102, 126, 234] }
                    });
                }

                addPDFFooter(doc);
                doc.save(`Inventory_Report_${new Date().toISOString().split('T')[0]}.pdf`);
                showAlert('Inventory Report PDF imetengenezwa!', 'success');

            } catch (error) {
                console.error('PDF Export Error:', error);
                showAlert('Hitilafu imetokea wakati wa kutengeneza PDF', 'warning');
            }
        }

        // Export Customer Report to PDF
        async function exportCustomerReportPDF() {
            showAlert('Inatengeneza Customer Report PDF...', 'success');
            
            try {
                const customerData = await apiCall('api.php?action=get_customers');
                
                if (!customerData || customerData.length === 0) {
                    showAlert('Hakuna data ya wateja', 'warning');
                    return;
                }

                const doc = new jsPDF();
                let yPos = addPDFHeader(doc, 'CUSTOMER DATABASE REPORT');

                // Summary stats
                yPos += 10;
                doc.setFontSize(14);
                doc.setFont(undefined, 'bold');
                doc.text('MUHTASARI WA WATEJA', 20, yPos);
                
                yPos += 15;
                doc.setFontSize(12);
                doc.setFont(undefined, 'normal');
                
                const totalSpent = customerData.reduce((sum, customer) => sum + parseFloat(customer.total_spent || 0), 0);
                const totalVisits = customerData.reduce((sum, customer) => sum + parseInt(customer.visits || 0), 0);
                const averageSpent = totalSpent / customerData.length;

                const stats = [
                    ['Jumla Wateja:', customerData.length],
                    ['Jumla Ziara:', totalVisits],
                    ['Jumla Waliotumia:', formatCurrency(totalSpent)],
                    ['Wastani kwa Mteja:', formatCurrency(averageSpent)]
                ];

                stats.forEach(([label, value]) => {
                    doc.text(label, 20, yPos);
                    doc.setFont(undefined, 'bold');
                    doc.text(String(value), 80, yPos);
                    doc.setFont(undefined, 'normal');
                    yPos += 8;
                });

                // Customer Table
                yPos += 10;
                doc.setFont(undefined, 'bold');
                doc.text('ORODHA YA WATEJA', 20, yPos);
                yPos += 5;

                const customerTableData = customerData.map(customer => [
                    customer.name,
                    customer.visits,
                    customer.last_visit,
                    customer.favorite_flavor || 'N/A',
                    formatCurrency(customer.total_spent)
                ]);

                doc.autoTable({
                    startY: yPos,
                    head: [['Jina', 'Ziara', 'Last Visit', 'Favorite Flavor', 'Total Spent']],
                    body: customerTableData,
                    theme: 'grid',
                    headStyles: { fillColor: [102, 126, 234] }
                });

                addPDFFooter(doc);
                doc.save(`Customer_Report_${new Date().toISOString().split('T')[0]}.pdf`);
                showAlert('Customer Report PDF imetengenezwa!', 'success');

            } catch (error) {
                console.error('PDF Export Error:', error);
                showAlert('Hitilafu imetokea wakati wa kutengeneza PDF', 'warning');
            }
        }

        // Export Financial Report to PDF
        async function exportFinancialReportPDF() {
            showAlert('Inatengeneza Financial Report PDF...', 'success');
            
            try {
                const [paymentsData, dailyStats] = await Promise.all([
                    apiCall('api.php?action=get_payments'),
                    apiCall('api.php?action=get_daily_stats')
                ]);
                
                if (!paymentsData || paymentsData.length === 0) {
                    showAlert('Hakuna data ya malipo', 'warning');
                    return;
                }

                const doc = new jsPDF();
                let yPos = addPDFHeader(doc, 'FINANCIAL ANALYSIS REPORT');

                // Financial Summary
                yPos += 10;
                doc.setFontSize(14);
                doc.setFont(undefined, 'bold');
                doc.text('MUHTASARI WA KIFEDHA', 20, yPos);
                
                yPos += 15;
                doc.setFontSize(12);
                doc.setFont(undefined, 'normal');
                
                const totalRevenue = paymentsData.reduce((sum, payment) => sum + parseFloat(payment.amount || 0), 0);
                const todayRevenue = dailyStats?.daily_revenue || 0;
                const averageTransaction = totalRevenue / paymentsData.length;

                // Payment method breakdown
                const paymentMethods = {};
                paymentsData.forEach(payment => {
                    const method = payment.payment_method;
                    if (!paymentMethods[method]) {
                        paymentMethods[method] = { count: 0, total: 0 };
                    }
                    paymentMethods[method].count++;
                    paymentMethods[method].total += parseFloat(payment.amount || 0);
                });

                const stats = [
                    ['Jumla Mapato (All Time):', formatCurrency(totalRevenue)],
                    ['Mapato Leo:', formatCurrency(todayRevenue)],
                    ['Jumla Malipo:', paymentsData.length],
                    ['Wastani wa Malipo:', formatCurrency(averageTransaction)]
                ];

                stats.forEach(([label, value]) => {
                    doc.text(label, 20, yPos);
                    doc.setFont(undefined, 'bold');
                    doc.text(String(value), 90, yPos);
                    doc.setFont(undefined, 'normal');
                    yPos += 8;
                });

                // Payment Methods Table
                yPos += 10;
                doc.setFont(undefined, 'bold');
                doc.text('BREAKDOWN YA NJIA ZA MALIPO', 20, yPos);
                yPos += 5;

                const methodData = Object.entries(paymentMethods).map(([method, data]) => [
                    method,
                    data.count,
                    formatCurrency(data.total),
                    `${((data.total / totalRevenue) * 100).toFixed(1)}%`
                ]);

                doc.autoTable({
                    startY: yPos,
                    head: [['Njia ya Malipo', 'Idadi', 'Kiasi', 'Asilimia']],
                    body: methodData,
                    theme: 'grid',
                    headStyles: { fillColor: [102, 126, 234] }
                });
                yPos = doc.lastAutoTable.finalY + 10;

                // Recent Transactions
                if (yPos > 220) {
                    doc.addPage();
                    yPos = 20;
                }

                doc.setFont(undefined, 'bold');
                doc.text('MALIPO YA HIVI KARIBUNI (20 ya mwisho)', 20, yPos);
                yPos += 5;

                const recentTransactions = paymentsData.slice(-20).reverse().map(payment => [
                    payment.customer_name,
                    formatCurrency(payment.amount),
                    payment.payment_method,
                    payment.transaction_date,
                    payment.transaction_time
                ]);

                doc.autoTable({
                    startY: yPos,
                    head: [['Mteja', 'Kiasi', 'Njia', 'Tarehe', 'Muda']],
                    body: recentTransactions,
                    theme: 'grid',
                    headStyles: { fillColor: [102, 126, 234] },
                    styles: { fontSize: 8 }
                });

                addPDFFooter(doc);
                doc.save(`Financial_Report_${new Date().toISOString().split('T')[0]}.pdf`);
                showAlert('Financial Report PDF imetengenezwa!', 'success');

            } catch (error) {
                console.error('PDF Export Error:', error);
                showAlert('Hitilafu imetokea wakati wa kutengeneza PDF', 'warning');
            }
        }

        // Export Custom Date Range Report to PDF
        async function exportCustomRangePDF() {
            const startDate = document.getElementById('pdfStartDate').value;
            const endDate = document.getElementById('pdfEndDate').value;
            
            if (!startDate || !endDate) {
                showAlert('Tafadhali chagua tarehe za kuanza na kumaliza', 'warning');
                return;
            }
            
            if (new Date(startDate) > new Date(endDate)) {
                showAlert('Tarehe ya kuanza lazima iwe kabla ya tarehe ya mwisho', 'warning');
                return;
            }

            showAlert('Inatengeneza Custom Range Report PDF...', 'success');
            
            try {
                // Get data for the custom range
                const [paymentsData, customersData] = await Promise.all([
                    apiCall('api.php?action=get_payments'),
                    apiCall('api.php?action=get_customers')
                ]);
                
                // Filter payments within date range
                const filteredPayments = paymentsData.filter(payment => 
                    payment.transaction_date >= startDate && payment.transaction_date <= endDate
                );

                if (filteredPayments.length === 0) {
                    showAlert('Hakuna data katika kipindi hicho', 'warning');
                    return;
                }

                const doc = new jsPDF();
                let yPos = addPDFHeader(doc, `CUSTOM RANGE REPORT (${startDate} - ${endDate})`);

                // Range Summary
                yPos += 10;
                doc.setFontSize(14);
                doc.setFont(undefined, 'bold');
                doc.text('MUHTASARI WA KIPINDI', 20, yPos);
                
                yPos += 15;
                doc.setFontSize(12);
                doc.setFont(undefined, 'normal');
                
                const totalRevenue = filteredPayments.reduce((sum, payment) => sum + parseFloat(payment.amount || 0), 0);
                const averageDaily = totalRevenue / (Math.ceil((new Date(endDate) - new Date(startDate)) / (1000 * 60 * 60 * 24)) + 1);
                const uniqueCustomers = [...new Set(filteredPayments.map(p => p.customer_name))].length;

                const stats = [
                    ['Kipindi:', `${startDate} hadi ${endDate}`],
                    ['Siku:', Math.ceil((new Date(endDate) - new Date(startDate)) / (1000 * 60 * 60 * 24)) + 1],
                    ['Jumla Mapato:', formatCurrency(totalRevenue)],
                    ['Wastani kwa Siku:', formatCurrency(averageDaily)],
                    ['Jumla Malipo:', filteredPayments.length],
                    ['Wateja wa Kipekee:', uniqueCustomers]
                ];

                stats.forEach(([label, value]) => {
                    doc.text(label, 20, yPos);
                    doc.setFont(undefined, 'bold');
                    doc.text(String(value), 80, yPos);
                    doc.setFont(undefined, 'normal');
                    yPos += 8;
                });

                // Daily breakdown for the range
                yPos += 10;
                doc.setFont(undefined, 'bold');
                doc.text('BREAKDOWN YA KILA SIKU', 20, yPos);
                yPos += 5;

                // Group by date
                const dailyData = {};
                filteredPayments.forEach(payment => {
                    const date = payment.transaction_date;
                    if (!dailyData[date]) {
                        dailyData[date] = { count: 0, total: 0 };
                    }
                    dailyData[date].count++;
                    dailyData[date].total += parseFloat(payment.amount || 0);
                });

                const dailyTableData = Object.entries(dailyData)
                    .sort(([a], [b]) => a.localeCompare(b))
                    .map(([date, data]) => [
                        date,
                        data.count,
                        formatCurrency(data.total)
                    ]);

                doc.autoTable({
                    startY: yPos,
                    head: [['Tarehe', 'Malipo', 'Mapato']],
                    body: dailyTableData,
                    theme: 'grid',
                    headStyles: { fillColor: [102, 126, 234] }
                });
                yPos = doc.lastAutoTable.finalY + 10;

                // Top customers in this period
                const customerTotals = {};
                filteredPayments.forEach(payment => {
                    const customer = payment.customer_name;
                    if (!customerTotals[customer]) {
                        customerTotals[customer] = { count: 0, total: 0 };
                    }
                    customerTotals[customer].count++;
                    customerTotals[customer].total += parseFloat(payment.amount || 0);
                });

                const topCustomers = Object.entries(customerTotals)
                    .sort(([,a], [,b]) => b.total - a.total)
                    .slice(0, 10);

                if (topCustomers.length > 0) {
                    if (yPos > 220) {
                        doc.addPage();
                        yPos = 20;
                    }

                    doc.setFont(undefined, 'bold');
                    doc.text('WATEJA WAKUU WA KIPINDI', 20, yPos);
                    yPos += 5;

                    const topCustomerData = topCustomers.map(([customer, data]) => [
                        customer,
                        data.count,
                        formatCurrency(data.total)
                    ]);

                    doc.autoTable({
                        startY: yPos,
                        head: [['Mteja', 'Ziara', 'Jumla Ametumia']],
                        body: topCustomerData,
                        theme: 'grid',
                        headStyles: { fillColor: [102, 126, 234] }
                    });
                }

                addPDFFooter(doc);
                doc.save(`Custom_Range_Report_${startDate}_to_${endDate}.pdf`);
                showAlert('Custom Range Report PDF imetengenezwa!', 'success');

            } catch (error) {
                console.error('PDF Export Error:', error);
                showAlert('Hitilafu imetokea wakati wa kutengeneza PDF', 'warning');
            }
        }

        // Initialize PDF date inputs with current date
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const lastWeek = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            
            document.getElementById('pdfEndDate').value = today;
            document.getElementById('pdfStartDate').value = lastWeek;
            
        });

        // Enhanced PDF with Charts (using Chart.js data if available)
        async function exportComprehensivePDF() {
            showAlert('Inatengeneza Comprehensive Business Report PDF...', 'success');
            
            try {
                // Get all data
                const [customers, flavors, inventory, payments, employees] = await Promise.all([
                    apiCall('api.php?action=get_customers'),
                    apiCall('api.php?action=get_flavors'),
                    apiCall('api.php?action=get_inventory'),
                    apiCall('api.php?action=get_payments'),
                    apiCall('api.php?action=get_employees')
                ]);

                const doc = new jsPDF();
                let yPos = addPDFHeader(doc, 'COMPREHENSIVE BUSINESS REPORT');

                // Executive Summary
                yPos += 10;
                doc.setFontSize(16);
                doc.setFont(undefined, 'bold');
                doc.text('EXECUTIVE SUMMARY', 20, yPos);
                
                yPos += 15;
                doc.setFontSize(12);
                doc.setFont(undefined, 'normal');
                
                const totalRevenue = payments.reduce((sum, payment) => sum + parseFloat(payment.amount || 0), 0);
                const totalCustomers = customers.length;
                const totalEmployees = employees.length;
                const averageTransaction = totalRevenue / payments.length;

                const executiveSummary = [
                    ['Business Performance Overview', ''],
                    ['Total Revenue (All Time):', formatCurrency(totalRevenue)],
                    ['Total Customers:', totalCustomers],
                    ['Total Transactions:', payments.length],
                    ['Average Transaction Value:', formatCurrency(averageTransaction)],
                    ['Active Employees:', totalEmployees],
                    ['Available Flavors:', flavors.length],
                    ['Inventory Items:', inventory.length]
                ];

                executiveSummary.forEach(([label, value]) => {
                    if (value === '') {
                        doc.setFont(undefined, 'bold');
                        doc.text(label, 20, yPos);
                        doc.setFont(undefined, 'normal');
                    } else {
                        doc.text(label, 20, yPos);
                        doc.setFont(undefined, 'bold');
                        doc.text(String(value), 100, yPos);
                        doc.setFont(undefined, 'normal');
                    }
                    yPos += 8;
                });

                // Business Insights
                yPos += 10;
                doc.setFont(undefined, 'bold');
                doc.text('BUSINESS INSIGHTS', 20, yPos);
                yPos += 10;
                doc.setFont(undefined, 'normal');

                // Top performing flavor
                const flavorPopularity = flavors.sort((a, b) => b.popularity - a.popularity);
                const topFlavor = flavorPopularity[0];
                
                // Most valuable customer
                const customerValue = customers.sort((a, b) => (b.total_spent || 0) - (a.total_spent || 0));
                const topCustomer = customerValue[0];

                // Critical stock items
                const criticalStock = inventory.filter(item => item.stock <= item.min_stock);

                const insights = [
                    `• Most Popular Flavor: ${topFlavor?.name} (${topFlavor?.popularity}% popularity)`,
                    `• Top Customer: ${topCustomer?.name} (${formatCurrency(topCustomer?.total_spent)})`,
                    `• Critical Stock Items: ${criticalStock.length} items need immediate attention`,
                    `• Average Customer Visits: ${(customers.reduce((sum, c) => sum + (c.visits || 0), 0) / customers.length).toFixed(1)}`,
                    `• Revenue per Employee: ${formatCurrency(totalRevenue / totalEmployees)}`
                ];

                insights.forEach(insight => {
                    doc.text(insight, 20, yPos);
                    yPos += 6;
                });

                // Add detailed tables on new pages
                doc.addPage();
                yPos = 20;

                // Top Customers Table
                doc.setFontSize(14);
                doc.setFont(undefined, 'bold');
                doc.text('TOP 15 CUSTOMERS', 20, yPos);
                yPos += 10;

                const topCustomersData = customers
                    .sort((a, b) => (b.total_spent || 0) - (a.total_spent || 0))
                    .slice(0, 15)
                    .map(customer => [
                        customer.name,
                        customer.visits || 0,
                        customer.favorite_flavor || 'N/A',
                        formatCurrency(customer.total_spent || 0)
                    ]);

                doc.autoTable({
                    startY: yPos,
                    head: [['Customer Name', 'Visits', 'Favorite Flavor', 'Total Spent']],
                    body: topCustomersData,
                    theme: 'grid',
                    headStyles: { fillColor: [102, 126, 234] }
                });
                yPos = doc.lastAutoTable.finalY + 20;

                // Flavor Performance Table
                doc.setFont(undefined, 'bold');
                doc.text('FLAVOR PERFORMANCE ANALYSIS', 20, yPos);
                yPos += 10;

                const flavorData = flavors.map(flavor => [
                    flavor.name,
                    flavor.stock,
                    formatCurrency(flavor.price),
                    `${flavor.popularity}%`,
                    flavor.stock <= flavor.min_stock ? 'LOW' : 'OK'
                ]);

                doc.autoTable({
                    startY: yPos,
                    head: [['Flavor', 'Stock', 'Price', 'Popularity', 'Status']],
                    body: flavorData,
                    theme: 'grid',
                    headStyles: { fillColor: [102, 126, 234] }
                });

                // Employee Summary
                doc.addPage();
                yPos = 20;

                doc.setFont(undefined, 'bold');
                doc.text('EMPLOYEE PERFORMANCE SUMMARY', 20, yPos);
                yPos += 10;

                const employeeData = employees.map(emp => [
                    emp.name,
                    emp.position,
                    emp.shift,
                    emp.services_count || 0,
                    formatCurrency(emp.salary || 0)
                ]);

                doc.autoTable({
                    startY: yPos,
                    head: [['Name', 'Position', 'Shift', 'Services', 'Salary']],
                    body: employeeData,
                    theme: 'grid',
                    headStyles: { fillColor: [102, 126, 234] }
                });

                addPDFFooter(doc);
                doc.save(`Comprehensive_Business_Report_${new Date().toISOString().split('T')[0]}.pdf`);
                showAlert('Comprehensive Business Report PDF imetengenezwa!', 'success');

            } catch (error) {
                console.error('PDF Export Error:', error);
                showAlert('Hitilafu imetokea wakati wa kutengeneza PDF', 'warning');
            }
        }

        // Add comprehensive report button
        function addComprehensiveReportButton() {
            const exportGrid = document.querySelector('.export-grid');
            if (exportGrid && !document.getElementById('comprehensiveReportBtn')) {
                const comprehensiveBtn = document.createElement('button');
                comprehensiveBtn.className = 'export-card';
                comprehensiveBtn.id = 'comprehensiveReportBtn';
                comprehensiveBtn.onclick = exportComprehensivePDF;
                comprehensiveBtn.innerHTML = `
                    <i class="fas fa-file-alt"></i>
                    <h4>Comprehensive Report</h4>
                    <p>Complete business analysis</p>
                `;
                exportGrid.appendChild(comprehensiveBtn);
            }
        }

        // Auto-generate file names with timestamp
        function generateFileName(reportType) {
            const timestamp = new Date().toISOString().split('T')[0];
            const time = new Date().toLocaleTimeString('en-US', { 
                hour12: false, 
                hour: '2-digit', 
                minute: '2-digit' 
            }).replace(':', '');
            return `${reportType}_${timestamp}_${time}.pdf`;
        }

        // Bulk PDF Export - Export all reports at once
        async function exportAllReportsPDF() {
            showAlert('Inatengeneza PDF zote... Hii itachukua muda kidogo.', 'success');
            
            const reports = [
                { name: 'Daily', func: exportDailyReportPDF },
                { name: 'Weekly', func: exportWeeklyReportPDF },
                { name: 'Monthly', func: exportMonthlyReportPDF },
                { name: 'Inventory', func: exportInventoryReportPDF },
                { name: 'Customer', func: exportCustomerReportPDF },
                { name: 'Financial', func: exportFinancialReportPDF }
            ];
            
            try {
                for (const report of reports) {
                    await new Promise(resolve => setTimeout(resolve, 1000)); // Wait 1 second between exports
                    await report.func();
                }
                showAlert('PDF zote zimetengenezwa kikamilifu!', 'success');
            } catch (error) {
                showAlert('Hitilafu imetokea wakati wa kutengeneza baadhi ya PDF', 'warning');
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            addComprehensiveReportButton();
            
            // Add bulk export button
            const pdfControls = document.querySelector('.pdf-controls');
            if (pdfControls) {
                const bulkExportDiv = document.createElement('div');
                bulkExportDiv.style.cssText = 'margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; text-align: center;';
                bulkExportDiv.innerHTML = `
                    <h4>Bulk Export</h4>
                    <button class="btn-pdf" onclick="exportAllReportsPDF()" style="margin: 0 10px;">
                        <i class="fas fa-download"></i> Export All Reports
                    </button>
                    <button class="btn-pdf" onclick="exportComprehensivePDF()" style="margin: 0 10px;">
                        <i class="fas fa-chart-pie"></i> Business Analysis
                    </button>
                `;
                pdfControls.appendChild(bulkExportDiv);
            }
        });

        // Utility function for alert (assuming it exists in main system)
        function showAlert(message, type) {
            // This should reference the main system's showAlert function
            if (window.parent && window.parent.showAlert) {
                window.parent.showAlert(message, type);
            } else {
                alert(message); // Fallback
            }
        }

        // Utility function for API calls (assuming it exists in main system)
        async function apiCall(endpoint, method = 'GET', data = null) {
            // This should reference the main system's apiCall function
            if (window.parent && window.parent.apiCall) {
                return await window.parent.apiCall(endpoint, method, data);
            } else {
                // Fallback implementation
                const config = {
                    method: method,
                    headers: { 'Content-Type': 'application/json' }
                };
                if (data && method === 'POST') {
                    config.body = JSON.stringify(data);
                }
                const response = await fetch(endpoint, config);
                return await response.json();
            }
        }
    </script>

    <script src="assets/js/script.js"></script>
</body>
</html>