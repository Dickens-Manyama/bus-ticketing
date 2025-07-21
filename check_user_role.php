<?php
// MySQL PDO connection removed
    
    // Check all users and their roles
    // $stmt = $pdo->query('SELECT id, username, email, role, status FROM user');
    // $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // echo "All users in database:\n";
    // foreach ($users as $user) {
    //     echo sprintf(
    //         "ID: %d, Username: %s, Email: %s, Role: %s, Status: %d\n",
    //         $user['id'],
    //         $user['username'],
    //         $user['email'],
    //         $user['role'] ?? 'NULL',
    //         $user['status']
    //     );
    // }
    
    // Check if there are any users with admin or superadmin role
    // $stmt = $pdo->query('SELECT id, username, email, role FROM user WHERE role IN ("admin", "superadmin")');
    // $adminUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // echo "\nAdmin users:\n";
    // if (empty($adminUsers)) {
    //     echo "No admin users found!\n";
    // } else {
    //     foreach ($adminUsers as $user) {
    //         echo sprintf(
    //             "ID: %d, Username: %s, Email: %s, Role: %s\n",
    //             $user['id'],
    //             $user['username'],
    //             $user['email'],
    //             $user['role']
    //         );
    //     }
    // }
    
 catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} 