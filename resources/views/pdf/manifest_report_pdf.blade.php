<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Report PDF</title>

    <!-- Import Inter Font from Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Internal CSS -->
    <style>
        body {
            background-color: white;
            color: #1a202c;
            font-family: 'Inter', sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 100%; /* Ensure the image takes full width */
            max-width: 700px; /* Adjusted max width */
            height: auto; /* Maintain aspect ratio */
            display: block;
            margin: 0 auto; /* Center the image */
        }

        .report-title {
            font-size: 15px;
            font-weight: bold;
            margin-top: 5px;
            text-align: center;
        }

        .container {
            background-color: white;
            border-radius: 6px;
            -webkit-box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .table-wrapper {
            max-height: 320px;
            margin-top: 8px;
            overflow-x: auto; /* Enable horizontal scrolling */
        }

        table {
            width: 100%; /* Ensure the table takes full width */
            table-layout: fixed; /* Ensure that columns take equal space */
            border-collapse: collapse;
            border-spacing: 0;
            border: 2px solid black;
        }

        th{
            padding: 6px; /* Adjusted padding */
            text-align: center;
            font-size: 10px; /* Reduced font size */
            word-wrap: break-word; /* Ensure text wraps within cells */
            border: 1px solid black;
        } 
        
        td {
            padding: 6px; /* Adjusted padding */
            text-align: center;
            font-size: 14px; /* Reduced font size */
            word-wrap: break-word; /* Ensure text wraps within cells */
            border: 1px solid black;
        }

        thead {
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        tbody {
            background-color: white;
        }

        .chart-image {
            text-align: center;
            margin-top: 20px;
            border-style: outset;
            border-width: 2px;
        }
    </style>
</head>
<body>

    <div class="header">
        <!-- Add the full image banner -->
        <img src="{{ public_path('Images/prf_header.png') }}" alt="MMDA Pasig River Ferry Header">
    </div>

    <div class="report-title">
        <h2>Manifest Report</h2>
    </div>

    <div class="container">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        @foreach($stations as $station)
                            <th>{{ $station }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        $categories = [
                            'total_manifest' => 'Total Manifest',
                            'regular' => 'Regular',
                            'student' => 'Student',
                            'senior' => 'Senior',
                            'pwd' => 'PWD',
                            'ticket_sold' => 'Ticket Sold',
                            'free_ride' => 'Free Ride',
                            'cash_collected' => 'Cash Collected',
                            'vessel_trip' => 'Vessel Trip'
                        ];
                    @endphp

                    @foreach($categories as $key => $category)
                        <tr>
                            <td>{{ $category }}</td>
                            @foreach($stations as $station)
                                <td>{{ $stationData[$station][$key] ?? '-' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
