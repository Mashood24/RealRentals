<?php
require_once __DIR__.'/../includes/MpesaService.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $mpesa = new MpesaService();
        $response = $mpesa->stkPush(
            $_POST['phone'],
            $_POST['amount'],
            $_POST['reference']
        );
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Payment request sent to your phone',
            'data' => $response
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Rent | cREN Rentals</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2a9d8f;
            --secondary: #264653;
            --accent: #e9c46a;
            --light: #f8f9fa;
            --dark: #1a1a1a;
            --success: #4caf50;
            --warning: #ff9800;
            --error: #f44336;
        }

        /* Base styles from existing design */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        .header {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            padding: 60px 0 30px;
            position: relative;
            overflow: hidden;
        }

        .header-content {
            position: relative;
            z-index: 1;
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .breadcrumb a {
            color: white;
            opacity: 0.8;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .breadcrumb a:hover {
            opacity: 1;
            text-decoration: underline;
        }

        .header h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 40px;
        }

        .card {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.5rem;
            color: var(--secondary);
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background-color: var(--primary);
        }

        .btn {
            display: inline-block;
            padding: 14px 30px;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            width: 100%;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: #238f81;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
            margin-top: 15px;
        }

        .btn-outline:hover {
            background-color: rgba(42, 157, 143, 0.1);
        }

        /* Pay Rent specific styles */
        .payment-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .payment-option {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            border-color: var(--primary);
        }

        .payment-option.selected {
            border-color: var(--primary);
            background-color: rgba(42, 157, 143, 0.1);
        }

        .payment-option img {
            height: 30px;
            margin-bottom: 10px;
            object-fit: contain;
        }

        .payment-option p {
            font-size: 14px;
            color: #555;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group input:focus {
            border-color: var(--primary);
            outline: none;
        }

        .card-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .card-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }

        .input-group {
            position: relative;
        }

        .save-card {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .save-card input {
            width: auto;
        }

        .save-card label {
            font-size: 14px;
            color: #555;
            cursor: pointer;
        }

        .summary-sidebar {
            position: sticky;
            top: 100px;
            align-self: start;
        }

        .booking-summary {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .summary-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .summary-item .label {
            color: #555;
        }

        .summary-item .value {
            font-weight: 500;
        }

        .summary-total {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.2rem;
        }

        .room-preview {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .room-image {
            width: 100px;
            height: 80px;
            border-radius: 5px;
            overflow: hidden;
        }

        .room-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .room-info h4 {
            font-size: 1rem;
            margin-bottom: 5px;
            color: var(--secondary);
        }

        .room-info p {
            font-size: 14px;
            color: #777;
        }

        .secure-payment {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            color: #777;
            font-size: 14px;
        }

        .secure-payment i {
            color: var(--primary);
        }

        .payment-success {
            display: none;
            text-align: center;
            padding: 30px;
            background-color: rgba(76, 175, 80, 0.1);
            border-radius: 5px;
            margin-top: 30px;
        }

        .payment-success i {
            font-size: 3rem;
            color: var(--success);
            margin-bottom: 15px;
        }

        .payment-success h3 {
            color: var(--success);
            margin-bottom: 10px;
        }

        .error-message {
            color: var(--error);
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
            
            .summary-sidebar {
                position: static;
                order: -1;
                margin-bottom: 40px;
            }
            
            .payment-options {
                grid-template-columns: 1fr 1fr;
            }
            
            .card-details {
                grid-template-columns: 1fr;
            }
            
            .header {
                padding: 40px 0 20px;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 576px) {
            .payment-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="breadcrumb">
                <a href="account.html">My Account</a>
                <i class="fas fa-chevron-right"></i>
                <span>Pay Rent</span>
            </div>
            <h1>Pay Your Rent</h1>
        </div>
    </header>

    <div class="container">
        <main class="card">
            <h2 class="section-title">Payment Method</h2>
            
            <div class="payment-options">
                <div class="payment-option selected" id="creditCardOption">
                    <img src="https://cdn-icons-png.flaticon.com/512/179/179457.png" alt="Credit Card">
                    <p>Credit/Debit Card</p>
                </div>
                <div class="payment-option" id="mpesaOption">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/M-PESA_LOGO-01.svg/1200px-M-PESA_LOGO-01.svg.png" alt="M-Pesa">
                    <p>M-Pesa</p>
                </div>
                <div class="payment-option" id="bankOption">
                    <img src="https://cdn-icons-png.flaticon.com/512/2489/2489076.png" alt="Bank Transfer">
                    <p>Bank Transfer</p>
                </div>
            </div>

            <form id="paymentForm">
                <div id="creditCardForm">
                    <div class="form-group">
                        <label for="cardNumber">Card Number</label>
                        <div class="input-group">
                            <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19">
                            <i class="fas fa-credit-card card-icon"></i>
                        </div>
                        <div class="error-message" id="cardNumberError">Please enter a valid card number</div>
                    </div>

                    <div class="form-group">
                        <label for="cardName">Name on Card</label>
                        <input type="text" id="cardName" placeholder="John Doe">
                        <div class="error-message" id="cardNameError">Please enter the name on your card</div>
                    </div>

                    <div class="card-details">
                        <div class="form-group">
                            <label for="expiryDate">Expiration Date</label>
                            <input type="text" id="expiryDate" placeholder="MM/YY" maxlength="5">
                            <div class="error-message" id="expiryDateError">Please enter a valid expiration date</div>
                        </div>
                        <div class="form-group">
                            <label for="cvv">Security Code (CVV)</label>
                            <div class="input-group">
                                <input type="text" id="cvv" placeholder="123" maxlength="4">
                                <i class="fas fa-question-circle card-icon" title="3-digit code on back of card"></i>
                            </div>
                            <div class="error-message" id="cvvError">Please enter a valid security code</div>
                        </div>
                    </div>

                    <div class="save-card">
                        <input type="checkbox" id="saveCard" checked>
                        <label for="saveCard">Save card for future payments</label>
                    </div>
                </div>

                <div id="mpesaForm" style="display: none;">
                    <div class="form-group">
                        <label for="mpesaNumber">M-Pesa Phone Number</label>
                        <input type="tel" id="mpesaNumber" placeholder="07XX XXX XXX">
                        <div class="error-message" id="mpesaNumberError">Please enter a valid M-Pesa number</div>
                    </div>
                    <p style="color: #555; font-size: 14px; margin-bottom: 20px;">
                        You will receive a payment request on your phone. Please enter your M-Pesa PIN to complete the transaction.
                    </p>
                </div>

                <div id="bankForm" style="display: none;">
                    <div class="form-group">
                        <label>Bank Account Details</label>
                        <div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                            <p style="margin-bottom: 10px;"><strong>Bank Name:</strong> Equity Bank Kenya</p>
                            <p style="margin-bottom: 10px;"><strong>Account Name:</strong> cREN Rentals Limited</p>
                            <p style="margin-bottom: 10px;"><strong>Account Number:</strong> 1234567890</p>
                            <p style="margin-bottom: 10px;"><strong>Branch:</strong> Westlands</p>
                            <p><strong>Swift Code:</strong> EQBLKENA</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reference">Payment Reference</label>
                        <input type="text" id="reference" placeholder="Your name or account number">
                        <div class="error-message" id="referenceError">Please enter a payment reference</div>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 25px;">
                    <label for="paymentAmount">Payment Amount (KES)</label>
                    <input type="number" id="paymentAmount" value="8500" min="100">
                    <div class="error-message" id="amountError">Please enter a valid amount</div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="payNowBtn">Pay Now</button>

                <div class="secure-payment">
                    <i class="fas fa-lock"></i>
                    <span>Your payment is secured with SSL encryption</span>
                </div>

                <div class="payment-success" id="paymentSuccess">
                    <i class="fas fa-check-circle"></i>
                    <h3>Payment Successful!</h3>
                    <p>Your rent payment has been processed successfully.</p>
                    <p>Transaction ID: <strong id="transactionId">PAY-789456123</strong></p>
                    <button class="btn btn-outline" style="margin-top: 20px;">
                        <i class="fas fa-receipt"></i> View Receipt
                    </button>
                </div>
            </form>
        </main>

        <aside class="summary-sidebar">
            <div class="booking-summary">
                <h2 class="section-title">Rent Summary</h2>
                
                <div class="room-preview">
                    <div class="room-image">
                        <img src="https://images.unsplash.com/photo-1584622650111-993a426fbf0a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Shared Room">
                    </div>
                    <div class="room-info">
                        <h4>Cozy Shared Room</h4>
                        <p>Westlands, Nairobi</p>
                        <p>2 beds • 18 sqm</p>
                    </div>
                </div>

                <div class="summary-item">
                    <div class="label">Monthly Rent</div>
                    <div class="value">KES 8,500</div>
                </div>
                <div class="summary-item">
                    <div class="label">Due Date</div>
                    <div class="value">June 5, 2023</div>
                </div>
                <div class="summary-item">
                    <div class="label">Days Remaining</div>
                    <div class="value">7 days</div>
                </div>
                <div class="summary-item summary-total">
                    <div class="label">Amount Due</div>
                    <div class="value">KES 8,500</div>
                </div>
            </div>

            <div class="booking-summary">
                <h2 class="section-title">Payment History</h2>
                <div class="summary-item">
                    <div class="label">May 2023</div>
                    <div class="value">KES 8,500</div>
                </div>
                <div class="summary-item">
                    <div class="label">April 2023</div>
                    <div class="value">KES 8,500</div>
                </div>
                <div class="summary-item">
                    <div class="label">March 2023</div>
                    <div class="value">KES 8,500</div>
                </div>
                <a href="paymenthistory.html" style="display: block; text-align: right; margin-top: 10px; color: var(--primary); text-decoration: none; font-size: 14px;">
                    View Full History <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <button class="btn btn-outline" onclick="location.href='autopay.html'">
                <i class="fas fa-calendar-check"></i> Set Up Autopay
            </button>
        </aside>
    </div>

    <script>
        // Replace the form submission in your existing JavaScript with:
        document.getElementById('paymentForm').addEventListener('submit', async function(e) {
        e.preventDefault();
    
    // Reset error messages
        document.querySelectorAll('.error-message').forEach(el => {
            el.style.display = 'none';
        });

    // Get selected payment method
        const selectedMethod = document.querySelector('.payment-option.selected').id;
    
    if (selectedMethod === 'mpesaOption') {
        const phone = document.getElementById('mpesaNumber').value;
        const amount = document.getElementById('paymentAmount').value;
        const reference = 'RENT-' + Date.now(); // Generate unique reference
        
        try {
            const response = await fetch('process-payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    phone: phone,
                    amount: amount,
                    reference: reference,
                    description: 'Rent Payment'
                })
            });
            
            const result = await response.json();
            
            if (result.status === 'success') {
                // Show success UI
                document.getElementById('transactionId').textContent = reference;
                document.getElementById('paymentSuccess').style.display = 'block';
                
                // Scroll to success message
                window.scrollTo({
                    top: document.getElementById('paymentSuccess').offsetTop - 20,
                    behavior: 'smooth'
                });
            } else {
                alert('Error: ' + (result.message || 'Payment failed'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while processing your payment');
        }
    } else {
        // Handle other payment methods (credit card, bank transfer)
        // ... existing code ...
    }
});
        // mpesa payment
        document.getElementById('mpesaForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            const response = await fetch('index.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            alert(result.message);
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Payment method toggle
            const paymentOptions = document.querySelectorAll('.payment-option');
            const paymentForms = {
                creditCard: document.getElementById('creditCardForm'),
                mpesa: document.getElementById('mpesaForm'),
                bank: document.getElementById('bankForm')
            };

            paymentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove selected class from all options
                    paymentOptions.forEach(opt => opt.classList.remove('selected'));
                    
                    // Add selected class to clicked option
                    this.classList.add('selected');
                    
                    // Hide all forms
                    Object.values(paymentForms).forEach(form => form.style.display = 'none');
                    
                    // Show the selected form
                    if (this.id === 'creditCardOption') {
                        paymentForms.creditCard.style.display = 'block';
                    } else if (this.id === 'mpesaOption') {
                        paymentForms.mpesa.style.display = 'block';
                    } else if (this.id === 'bankOption') {
                        paymentForms.bank.style.display = 'block';
                    }
                });
            });

            // Format card number input
            document.getElementById('cardNumber').addEventListener('input', function(e) {
                let value = this.value.replace(/\s+/g, '');
                if (value.length > 0) {
                    value = value.match(new RegExp('.{1,4}', 'g')).join(' ');
                }
                this.value = value;
            });

            // Format expiry date input
            document.getElementById('expiryDate').addEventListener('input', function(e) {
                let value = this.value.replace(/\D+/g, '');
                if (value.length > 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                this.value = value;
            });

            // Form submission
            document.getElementById('paymentForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Reset error messages
                document.querySelectorAll('.error-message').forEach(el => {
                    el.style.display = 'none';
                });

                let isValid = true;

                // Validate based on selected payment method
                if (document.getElementById('creditCardOption').classList.contains('selected')) {
                    const cardNumber = document.getElementById('cardNumber').value.replace(/\s+/g, '');
                    if (cardNumber.length < 16 || !/^\d+$/.test(cardNumber)) {
                        document.getElementById('cardNumberError').style.display = 'block';
                        isValid = false;
                    }

                    if (document.getElementById('cardName').value.trim() === '') {
                        document.getElementById('cardNameError').style.display = 'block';
                        isValid = false;
                    }

                    const expiryDate = document.getElementById('expiryDate').value;
                    if (!/^\d{2}\/\d{2}$/.test(expiryDate)) {
                        document.getElementById('expiryDateError').style.display = 'block';
                        isValid = false;
                    }

                    const cvv = document.getElementById('cvv').value;
                    if (cvv.length < 3 || !/^\d+$/.test(cvv)) {
                        document.getElementById('cvvError').style.display = 'block';
                        isValid = false;
                    }
                } else if (document.getElementById('mpesaOption').classList.contains('selected')) {
                    const mpesaNumber = document.getElementById('mpesaNumber').value;
                    if (mpesaNumber.length < 10 || !/^07\d{8}$/.test(mpesaNumber)) {
                        document.getElementById('mpesaNumberError').style.display = 'block';
                        isValid = false;
                    }
                } else if (document.getElementById('bankOption').classList.contains('selected')) {
                    if (document.getElementById('reference').value.trim() === '') {
                        document.getElementById('referenceError').style.display = 'block';
                        isValid = false;
                    }
                }

                // Validate amount
                const amount = document.getElementById('paymentAmount').value;
                if (amount < 1000 || isNaN(amount)) {
                    document.getElementById('amountError').style.display = 'block';
                    isValid = false;
                }

                if (isValid) {
                    // In a real implementation, you would process the payment here
                    // For demo purposes, we'll just show the success message
                    
                    // Generate a random transaction ID
                    const transactionId = 'PAY-' + Math.floor(Math.random() * 1000000000);
                    document.getElementById('transactionId').textContent = transactionId;
                    
                    // Show success message
                    document.getElementById('paymentSuccess').style.display = 'block';
                    
                    // Scroll to success message
                    window.scrollTo({
                        top: document.getElementById('paymentSuccess').offsetTop - 20,
                        behavior: 'smooth'
                    });
                }
            });

            // Generate a random transaction ID for the demo
            function generateTransactionId() {
                return 'PAY-' + Math.floor(Math.random() * 1000000000);
            }
        });
    </script>
</body>
</html>