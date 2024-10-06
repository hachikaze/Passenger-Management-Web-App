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

    <center>
        <h2>Daily Ridership Report for {{ $month }}/{{ $year }}</h2>
    </center>

    <div class="container">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Ridership</th>
                        <th>Boats</th>
                        <th>Month to Date</th>
                        <th>Stations</th>
                        <th>Male</th>
                        <th>Female</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyData as $dayData)
                    <tr>
                        <td>{{ $dayData['date'] }}</td>
                        <td>{{ $dayData['ridership'] }}</td>
                        <td>{{ $dayData['boats'] }}</td>
                        <td>{{ $dayData['month_to_date'] }}</td>
                        <td>{{ $dayData['stations'] }}</td>
                        <td>{{ $dayData['male_passengers'] }}</td>
                        <td>{{ $dayData['female_passengers'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Embed the chart image -->
    @if ($chartImage)
        <div class="chart-image">
            <img src="{{ $chartImage }}" alt="Daily Ridership Chart" style="max-width: 90%; height: auto;">
        </div>
    @endif
    
</body>
</html>
