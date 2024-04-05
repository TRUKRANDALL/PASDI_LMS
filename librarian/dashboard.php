<?php 
include('header.php'); 
include('session.php'); 
include('navbar_borrow.php'); 

// Include your database connection file
include('dbcon.php');

// Query to fetch total number of books from the database
$query_books = "SELECT COUNT(*) AS total_books FROM book";
$result_books = mysqli_query($conn, $query_books);
$row_books = mysqli_fetch_assoc($result_books);
$total_books = $row_books['total_books'];

// Query to fetch total number of members from the database
$query_members = "SELECT COUNT(*) AS total_members FROM member";
$result_members = mysqli_query($conn, $query_members);
$row_members = mysqli_fetch_assoc($result_members);
$total_members = $row_members['total_members'];

// Query to fetch number of book borrowings today from the database
$query_borrowed_books_today = "SELECT COUNT(*) AS borrowed_books_today FROM borrowdetails WHERE DATE(date_borrow) = CURDATE()";
$result_borrowed_books_today = mysqli_query($conn, $query_borrowed_books_today);

if (!$result_borrowed_books_today) {
    die("Query failed: " . mysqli_error($conn));
}

$row_borrowed_books_today = mysqli_fetch_assoc($result_borrowed_books_today);
$borrowed_books_today = $row_borrowed_books_today['borrowed_books_today'];

// Query to fetch number of books returned today from the database
$query_returned_today = "SELECT COUNT(*) AS returned_today FROM borrowdetails WHERE DATE(date_return) = CURDATE()";
$result_returned_today = mysqli_query($conn, $query_returned_today);
$row_returned_today = mysqli_fetch_assoc($result_returned_today);
$returned_books_today = $row_returned_today['returned_today'];

// Calculate available books
$available_books = $total_books - $borrowed_books_today - $returned_books_today;

// Define custom colors with high contrast
$colors = array(
    'rgba(255, 99, 132, 0.8)', // Red (Returned Books Today) 
    'rgba(75, 192, 192, 0.8)', // Green (Total Members)
    'rgba(54, 162, 235, 0.8)', // Blue (Total Books)
    'rgba(130, 70, 255, 0.8)'  // Violet (Borrowed Books Today)
);
?>

<style>
    body {
        overflow: hidden;
        position: fixed;
        width: 100%;
    }
    .dashboard-heading {
        text-align: center;
        margin-right: 0%; /* Adjust as needed */
    }
    .chart-heading {
        text-align: center;
        margin-top: -20px; /* Adjust as needed */
        margin-left: 200px;
        margin-bottom: -50px;
    }

    /* Added styles for hover effect */
    .stat-box {
        margin-bottom: 15px;
        padding: 20px;
        border-radius: 0px;
        color: black;
        font-size: 26px;
        width: 200px; /* Increased width */
        margin-left: 10%;
        transition: transform 0.3s ease; /* Added transition for smooth scaling */
    }

    /* Apply hover effect only on devices with a screen width of 768px or larger */
    @media screen and (min-width: 1000px) {
        .stat-box:hover {
            transform: scale(1.2); /* Scale up the box by 10% on hover */
        }
    }
</style>

<div class="container">
    <div class="margin-top">
        <div class="row">   
            <div class="span12">
                <h2 class="dashboard-heading" style="font-weight: bold;">Dashboard</h2>
                <div class="dashboard-grid">
                    <div class="dashboard-stats">
                        <!-- Statistics Code -->
                        <div class="row">
                            <div class="span3">
                                <div class="stat-box available">
                                    <span class="label" style="background-color: initial;">Total Books</span>
                                    <span class="value"><a href="book.php"><?php echo $total_books; ?></a></span>
                                </div>
                            </div>
                            <div class="span3">
                                <div class="stat-box available" style="background-color: #4CAF50;">
                                    <span class="label" style="background-color: initial;">Total Members</span>
                                    <span class="value"><a href="member.php"><?php echo $total_members; ?></a></span>
                                </div>
                            </div>
                            <div class="span3">
                                <div class="stat-box issued">
                                    <span class="label" style="background-color: initial;">Returned Books (Today)</span>
                                    <span class="value"><?php echo $returned_books_today; ?></span>
                                </div>
                            </div>
                            <div class="span3">
                                <div class="stat-box issued" style="background-color: #8A2BE2;">
                                    <span class="label" style="background-color: initial;">Borrowed Books (Today)</span>
                                    <span class="value"><?php echo $borrowed_books_today; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dashboard-chart">
                        <!-- Pie Chart Code -->
                        <div class="row" style="margin-top: 20px;">
                            <div class="span12 text-center">
                                <h3 class="chart-heading">Books Status</h3>
                                <div id="chart-container" style="display: inline-block;">
                                    <canvas id="booksChart" width="580" height="500"></canvas>
                                </div>
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
                                <script>
                                    var ctx = document.getElementById('booksChart').getContext('2d');
                                    var myChart = new Chart(ctx, {
                                        type: 'pie',
                                        data: {
                                            labels: ['Total Books', 'Total Members', 'Returned Books (Today)', 'Borrowed Books (Today)'],
                                            datasets: [{
                                                label: 'Books Status',
                                                data: [
                                                    <?php echo $total_books; ?>,
                                                    <?php echo $total_members; ?>,
                                                    <?php echo $returned_books_today; ?>,
                                                    <?php echo $borrowed_books_today; ?>
                                                ],
                                                backgroundColor: <?php echo json_encode($colors); ?>,
                                                borderColor: '#fff',
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            responsive: false,
                                            plugins: {
                                                title: {
                                                    display: true,
                                                    text: ''
                                                    
                                                },
                                                legend: {
                                                    display: true,
                                                    position: 'left', // Adjusted position
                                                    align: 'center'   // Center alignment
                                                }
                                            }
                                        }
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>     
        </div>
    </div>
</div>

<?php include('footer.php') ?>


<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .dashboard-stats {
        /* Style your stats here if needed */
    }

    .dashboard-chart {
        /* Style your chart here if needed */
    }

    .stat-box {
        margin-bottom: 15px;
        padding: 20px;
        border-radius: 5px;
        color: black;
        font-size: 26px;
        width: 400px; /* Increased width */
        margin-left: 10%;
    }

    .stat-box .label {
        display: block;
        margin-bottom: 30px;
        font-size: 16px;
    }

    .stat-box .value {
        font-weight: bold;
    }

    .available {
        background-color: #E72929; /* Green */
    }

    .issued {
        background-color: #5356FF; /* Red */
    }

   
</style>
