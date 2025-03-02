<?php
require_once "controller\StudentController.php";

class Router {
    private array $routes = [];

    public function __construct() {
        $this->defineRoutes();
    }

    private function defineRoutes() {
        $this->routes = [
            'GET' => [
                'students' => [StudentController::class, 'getAllStud'],
                'students/{id}' => [StudentController::class, 'getStudById'],
            ],
            'POST' => [
                'student' => [StudentController::class, 'addStud'],
            ],
            'PUT' => [
                'students/update/{id}' => [StudentController::class, 'updateStud'],
            ],
            'DELETE' => [
                'student/delete/{id}' => [StudentController::class, 'deleteStud'],
            ],
        ];
    }

    public function handleRequest() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $this->getProcessedUri();
        $queryParams = $_GET; 

        error_log("Request Method: " . $requestMethod);
        error_log("Request URI: " . $requestUri);

        if (!isset($this->routes[$requestMethod])) {
            error_log("Request method not supported: " . $requestMethod);
            $this->sendNotFound();
            return;
        }

        if (isset($queryParams['id'])) {
            $studentId = $queryParams['id'];
            error_log("Query parameter detected: id=" . $studentId);

            switch ($requestUri) {
                case "students":
                    [$controllerClass, $method] = $this->routes['GET']['students/{id}'];
                    $this->dispatch([$controllerClass, $method], [$studentId]);
                    return;

                case "students/update":
                    [$controllerClass, $method] = $this->routes['PUT']['students/update/{id}'];
                    $this->dispatch([$controllerClass, $method], [$studentId, $this->getRequestData()]);
                    return;

                case "student/delete":
                    [$controllerClass, $method] = $this->routes['DELETE']['student/delete/{id}'];
                    $this->dispatch([$controllerClass, $method], [$studentId]);
                    return;
            }
        }

        foreach ($this->routes[$requestMethod] as $route => $handler) {
            $pattern = $this->convertToRegex($route);
            error_log("Checking route: " . $route . " with pattern: " . $pattern);

            if (preg_match($pattern, $requestUri, $matches)) {
                error_log("Route matched: " . $route);
                array_shift($matches);

                [$controllerClass, $method] = $handler;

                if ($requestMethod === 'POST') {
                    $this->dispatch([$controllerClass, $method], [$this->getRequestData()]);
                } else {
                    $this->dispatch([$controllerClass, $method], $matches);
                }
                return;
            }
        }

        error_log("No matching route found for URI: " . $requestUri);
        $this->sendNotFound();
    }

    private function getProcessedUri(): string {
        $requestUri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $scriptName = dirname($_SERVER["SCRIPT_NAME"]);
        return trim(str_replace($scriptName, "", $requestUri), "/");
    }

    private function convertToRegex(string $route): string {
        $pattern = preg_replace('/\{(\w+)\}/', '(\\d+)', $route); 
        return '/^' . str_replace('/', '\/', $pattern) . '$/';
    }

    private function getRequestData() {
        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : []; 
    }

    private function dispatch(array $handler, array $params) {
        [$controllerClass, $method] = $handler;
        $controller = new $controllerClass();

        call_user_func_array([$controller, $method], $params);
    }

    private function sendNotFound() {
        header("HTTP/1.0 404 Not Found");
        echo json_encode(["message" => "Route not found"]);
    }
}
?>