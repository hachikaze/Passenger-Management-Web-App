<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Manifest PDF</title>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            font-size: 14px; /* Smaller base font size for the entire document */
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 100%;
            max-width: 800px;
            height: auto;
        }

        .report-title {
            font-size: 12px; /* Slightly larger for titles */
            font-weight: bold;
            margin-top: 10px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th {
            padding: 5px; /* Reduced padding for a compact look */
            font-size: 11px; /* Smaller font size for table content */
            text-align: center;
        } 
        
        td {
            padding: 5px; /* Reduced padding for a compact look */
            font-size: 10px; /* Smaller font size for table content */
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('Images/prf_header.png') }}" alt="MMDA Pasig River Ferry Header">
    </div>

    <div class="report-title">
        <h2>Passenger Manifest</h2>
    </div>

    <p><strong>Boat Name:</strong> {{ $boat->boat_name }}</p>
    <p><strong>Boat Capacity:</strong> {{ $boat->max_capacity }}</p>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Contact #</th>
                <th>Profession</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Origin</th>
                <th>Destination</th>
                <th>Time In</th>
            </tr>
        </thead>
        <tbody>
            @foreach($passengers as $passenger)
            <tr>
                <td>{{ $passenger->first_name }} {{ $passenger->middle_name }} {{ $passenger->last_name }}</td>
                <td>{{ $passenger->address }}</td>
                <td>{{ $passenger->contact_number }}</td>
                <td>{{ $passenger->profession }}</td>
                <td>{{ $passenger->age }}</td>
                <td>{{ $passenger->gender }}</td>
                <td>{{ $passenger->origin }}</td>
                <td>{{ $passenger->destination }}</td>
                <td>{{ $passenger->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
