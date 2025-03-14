<?php

include('config.php'); 
include('auth.php');  

if (!isAuthenticated()) {
    http_response_code(401); 
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

$request = isset($_SERVER['PATH_INFO']) ? explode('/', trim($_SERVER['PATH_INFO'], '/')) : [];

switch ($method) {
    case 'GET':
        if (count($request) == 1) {
            $stmt = $conn->prepare("SELECT * FROM products");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($products);
        } elseif (count($request) == 2) {
            $id = intval($request[1]);
            $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($product) {
                echo json_encode($product);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Product not found"]);
            }
        }
        break;

    case 'POST':
        if (count($request) == 1) {
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($data['name'], $data['price'])) {
                $stmt = $conn->prepare("INSERT INTO products (name, price, description) VALUES (:name, :price, :description)");
                $stmt->bindParam(':name', $data['name']);
                $stmt->bindParam(':price', $data['price']);
                $stmt->bindParam(':description', $data['description']);
                if ($stmt->execute()) {
                    echo json_encode(["message" => "Product created successfully"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Error creating product"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Missing required fields"]);
            }
        }
        break;

    case 'PUT':
        if (count($request) == 2) {
            $id = intval($request[1]);
            $data = json_decode(file_get_contents("php://input"), true);
            if (isset($data['name'], $data['price'])) {
                $stmt = $conn->prepare("UPDATE products SET name = :name, price = :price, description = :description WHERE id = :id");
                $stmt->bindParam(':name', $data['name']);
                $stmt->bindParam(':price', $data['price']);
                $stmt->bindParam(':description', $data['description']);
                $stmt->bindParam(':id', $id);
                if ($stmt->execute()) {
                    echo json_encode(["message" => "Product updated successfully"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Error updating product"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Missing required fields"]);
            }
        }
        break;

    case 'DELETE':
        if (count($request) == 2) {
            $id = intval($request[1]);
            $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
            $stmt->bindParam(':id', $id);
            if ($stmt->execute()) {
                echo json_encode(["message" => "Product deleted successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Error deleting product"]);
            }
        }
        break;

    default:
        http_response_code(405); 
        echo json_encode(["error" => "Method Not Allowed"]);
        break;
}
?>
