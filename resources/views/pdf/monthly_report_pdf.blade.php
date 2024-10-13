<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Report PDF</title>

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
            max-width: 800px; /* You can adjust this based on the size */
            height: auto; /* Maintain aspect ratio */
            display: block;
            margin: 0 auto; /* Center the image */
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
        }

        table {
            min-width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            border: 2px solid black;
        }

        thead {
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            padding: 12px;
            text-align: center;
            font-size: 12px;
            font-weight: normal;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-family: 'Inter', sans-serif;
            border: 1px solid black;
        }

        td {
            padding: 8px;
            text-align: center;
            font-size: 12px;
            color: #1a202c;
            font-family: 'Inter', sans-serif;
            border: 1px solid black;
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

    <center>
        <h2>Monthly Ridership Report for {{ $year }}</h2>
    </center>
    
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Ridership</th>
                <th>Student</th>
                <th>Senior</th>
                <th>Male</th>
                <th>Female</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($monthlyData as $data)
                <tr>
                    <td>{{ $data['month'] }}</td>
                    <td>{{ $data['ridership'] }}</td>
                    <td>{{ $data['student'] }}</td>
                    <td>{{ $data['senior'] }}</td>
                    <td>{{ $data['male'] }}</td>
                    <td>{{ $data['female'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($monthlyChartImage)
        <!-- Embedding the base64 chart image -->
        <div class="chart-image">
            <img src="{{ $monthlyChartImage }}" alt="Monthly Ridership Chart" style="max-width: 90%; height: auto;" />
        </div>
    @else
        <p>No chart data available.</p>
    @endif

</body>
</html>
