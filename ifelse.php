<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Control System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            color: #333;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            padding: 10px 15px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4cae4c;
        }
        .result {
            margin-top: 20px;
            padding: 10px;
            background-color: #e7f3fe;
            border: 1px solid #b3d4fc;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<h1>Access Control System</h1>

<form method="POST">
    <label for="userRole">User  Role:</label>
    <select name="userRole" id="userRole" required>
        <option value="Admin">Admin</option>
        <option value="Editor">Editor</option>
        <option value="Viewer">Viewer</option>
        <option value="Guest">Guest</option>
    </select>

    <label for="resourceType">Resource Type:</label>
    <select name="resourceType" id="resourceType" required>
        <option value="Public">Public</option>
        <option value="Restricted">Restricted</option>
        <option value="Confidential">Confidential</option>
    </select>

    <label for="resourceFlags">Resource Flags (premium):</label>
    <input type="checkbox" name="resourceFlags[premium]" id="premium" value="true">
    
    <label for="currentTime">Current Time (HH:MM):</label>
    <input type="time" name="currentTime" id="currentTime" required>

    <label for="isSuspended">Is Suspended:</label>
    <select name="isSuspended" id="isSuspended" required>
        <option value="false">No</option>
        <option value="true">Yes</option>
    </select>

    <button type="submit">Check Access</button>
</form>

<?php
// Function to check user access to resources
function checkAccess($userRole, $resourceType, $resourceFlags, $currentTime, $isSuspended) {
    // Define valid roles and resource types
    $validRoles = ['Admin', 'Editor', 'Viewer', 'Guest'];
    $validResourceTypes = ['Public', 'Restricted', 'Confidential'];

    // Validate user role
    if (!in_array($userRole, $validRoles)) {
        return "Error: Unsupported user role '$userRole'.";
    }

    // Validate resource type
    if (!in_array($resourceType, $validResourceTypes)) {
        return "Error: Unsupported resource type '$resourceType'.";
    }

    // Check if user is suspended
    if ($isSuspended) {
        return "Access denied: User is suspended.";
    }

    // Convert current time to a timestamp for comparison
    $currentHour = (int)explode(':', $currentTime)[0];

    // Check time-based restrictions for non-admin users
    if ($userRole !== 'Admin' && ($currentHour >= 0 && $currentHour < 6)) {
        return "Access denied: Access is restricted between 12:00 AM and 6:00 AM for non-admin users.";
    }

    // Determine access based on user role and resource type
    switch ($resourceType) {
        case 'Public':
            // Check for premium flag
            if (isset($resourceFlags['premium']) && $resourceFlags['premium']) {
                if ($userRole === 'Admin' || $userRole === 'Editor') {
                    return "Access granted to premium public resource.";
                } else {
                    return "Access denied: Premium public resource requires Admin or Editor privileges.";
                }
            }
            return "Access granted to public resource.";

        case 'Restricted':
            if ($userRole === 'Admin' || $userRole === 'Editor') {
                return "Access granted to restricted resource.";
            }
            return "Access denied: Restricted resource requires Admin or Editor privileges.";

        case 'Confidential':
            if ($userRole === 'Admin') {
                return "Access granted to confidential resource.";
            }
            return "Access denied: Confidential resource requires Admin privileges.";
    }

    return "Access denied: Unknown error.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userRole = $_POST['userRole'];
    $resourceType = $_POST['resourceType'];
    $resourceFlags = isset($_POST['resourceFlags']) ? $_POST['resourceFlags'] : [];
    $currentTime = $_POST['currentTime'];
    $isSuspended = $_POST['isSuspended'] === 'true';

    // Check access and display the result
    $result = checkAccess($userRole, $resourceType, $resourceFlags, $currentTime, $isSuspended);
    echo "<div class='result'>$result</div>";
}
?>

</body>
</html>