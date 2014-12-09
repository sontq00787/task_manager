<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Ravi Tamada
 * @link URL Tutorial link
 */
class DbHandler {
	private $conn;
	function __construct() {
		require_once dirname ( __FILE__ ) . '/DbConnect.php';
		// opening db connection
		$db = new DbConnect ();
		$this->conn = $db->connect ();
	}
	
	/* ------------- `users` table method ------------------ */
	
	/**
	 * Creating new user
	 *
	 * @param String $name
	 *        	User full name
	 * @param String $email
	 *        	User login email id
	 * @param String $password
	 *        	User login password
	 */
	public function createUser($name, $email, $password) {
		require_once 'PassHash.php';
		$response = array ();
		
		// First check if user already existed in db
		if (! $this->isUserExists ( $email )) {
			// Generating password hash
			$password_hash = PassHash::hash ( $password );
			
			// Generating API key
			$api_key = $this->generateApiKey ();
			
			// insert query
			$stmt = $this->conn->prepare ( "INSERT INTO users(name, email, password_hash, api_key, status) values(?, ?, ?, ?, 1)" );
			$stmt->bind_param ( "ssss", $name, $email, $password_hash, $api_key );
			
			$result = $stmt->execute ();
			
			$stmt->close ();
			
			// Check for successful insertion
			if ($result) {
				// User successfully inserted
				return USER_CREATED_SUCCESSFULLY;
			} else {
				// Failed to create user
				return USER_CREATE_FAILED;
			}
		} else {
			// User with same email already existed in the db
			return USER_ALREADY_EXISTED;
		}
		
		return $response;
	}
	
	/**
	 * Checking user login
	 *
	 * @param String $email
	 *        	User login email id
	 * @param String $password
	 *        	User login password
	 * @return boolean User login status success/fail
	 */
	public function checkLogin($email, $password) {
		require_once 'PassHash.php';
		// fetching user by email
		$stmt = $this->conn->prepare ( "SELECT password_hash FROM users WHERE email = ?" );
		
		$stmt->bind_param ( "s", $email );
		
		$stmt->execute ();
		
		$stmt->bind_result ( $password_hash );
		
		$stmt->store_result ();
		
		if ($stmt->num_rows > 0) {
			// Found user with the email
			// Now verify the password
			
			$stmt->fetch ();
			
			$stmt->close ();
			
			if (PassHash::check_password ( $password_hash, $password )) {
				// User password is correct
				return TRUE;
			} else {
				// user password is incorrect
				return FALSE;
			}
		} else {
			$stmt->close ();
			
			// user not existed with the email
			return FALSE;
		}
	}
	
	/**
	 * Checking for duplicate user by email address
	 *
	 * @param String $email
	 *        	email to check in db
	 * @return boolean
	 */
	private function isUserExists($email) {
		$stmt = $this->conn->prepare ( "SELECT id from users WHERE email = ?" );
		$stmt->bind_param ( "s", $email );
		$stmt->execute ();
		$stmt->store_result ();
		$num_rows = $stmt->num_rows;
		$stmt->close ();
		return $num_rows > 0;
	}
	/**
	 * Fetching all users
	 */
	public function getAllUsers() {
		$stmt = $this->conn->prepare ( "SELECT id,name,email FROM users" );
		$stmt->execute ();
		$users = $stmt->get_result ();
		$stmt->close ();
		return $users;
	}
	/**
	 * Fetching user by email
	 *
	 * @param String $email
	 *        	User email id
	 */
	public function getUserByEmail($email) {
		$stmt = $this->conn->prepare ( "SELECT id, name, email, api_key, status, created_at FROM users WHERE email = ?" );
		$stmt->bind_param ( "s", $email );
		if ($stmt->execute ()) {
			// $user = $stmt->get_result()->fetch_assoc();
			$stmt->bind_result ( $id, $name, $email, $api_key, $status, $created_at );
			$stmt->fetch ();
			$user = array ();
			$user ["id"] = $id;
			$user ["name"] = $name;
			$user ["email"] = $email;
			$user ["api_key"] = $api_key;
			$user ["status"] = $status;
			$user ["created_at"] = $created_at;
			$stmt->close ();
			return $user;
		} else {
			return NULL;
		}
	}
	
	/**
	 * Fetching user by userid
	 *
	 * @param String $user_id
	 *        	User id
	 */
	public function getUserByID($user_id) {
		$stmt = $this->conn->prepare ( "SELECT name, email, api_key, status, created_at FROM users WHERE id = ?" );
		$stmt->bind_param ( "i", $user_id );
		if ($stmt->execute ()) {
			// $user = $stmt->get_result()->fetch_assoc();
			$stmt->bind_result ( $name, $email, $api_key, $status, $created_at );
			$stmt->fetch ();
			$user = array ();
			$user ["name"] = $name;
			$user ["email"] = $email;
			$user ["api_key"] = $api_key;
			$user ["status"] = $status;
			$user ["created_at"] = $created_at;
			$stmt->close ();
			return $user;
		} else {
			return NULL;
		}
	}
	
	/**
	 * Fetching user api key
	 *
	 * @param String $user_id
	 *        	user id primary key in user table
	 */
	public function getApiKeyById($user_id) {
		$stmt = $this->conn->prepare ( "SELECT api_key FROM users WHERE id = ?" );
		$stmt->bind_param ( "i", $user_id );
		if ($stmt->execute ()) {
			// $api_key = $stmt->get_result()->fetch_assoc();
			// TODO
			$stmt->bind_result ( $api_key );
			$stmt->close ();
			return $api_key;
		} else {
			return NULL;
		}
	}
	
	/**
	 * Fetching user id by api key
	 *
	 * @param String $api_key
	 *        	user api key
	 */
	public function getUserId($api_key) {
		$stmt = $this->conn->prepare ( "SELECT id FROM users WHERE api_key = ?" );
		$stmt->bind_param ( "s", $api_key );
		if ($stmt->execute ()) {
			$stmt->bind_result ( $user_id );
			$stmt->fetch ();
			// TODO
			// $user_id = $stmt->get_result()->fetch_assoc();
			$stmt->close ();
			return $user_id;
		} else {
			return NULL;
		}
	}
	
	/**
	 * Validating user api key
	 * If the api key is there in db, it is a valid key
	 *
	 * @param String $api_key
	 *        	user api key
	 * @return boolean
	 */
	public function isValidApiKey($api_key) {
		$stmt = $this->conn->prepare ( "SELECT id from users WHERE api_key = ?" );
		$stmt->bind_param ( "s", $api_key );
		$stmt->execute ();
		$stmt->store_result ();
		$num_rows = $stmt->num_rows;
		$stmt->close ();
		return $num_rows > 0;
	}
	
	/**
	 * Generating random Unique MD5 String for user Api key
	 */
	private function generateApiKey() {
		return md5 ( uniqid ( rand (), true ) );
	}
	
	/* ------------- `tasks` table method ------------------ */
	
	/**
	 * Creating new task
	 *
	 * @param String $user_id
	 *        	user id to whom task belongs to
	 * @param String $task
	 *        	task text
	 */
	public function createTask($user_id, $task, $project_id) {
		$stmt = $this->conn->prepare ( "INSERT INTO tasks(task,project_id) VALUES(?,?)" );
		$stmt->bind_param ( "si", $task, $project_id );
		$result = $stmt->execute ();
		$stmt->close ();
		
		if ($result) {
			// task row created
			// now assign the task to user
			$new_task_id = $this->conn->insert_id;
			$res = $this->createUserTask ( $user_id, $new_task_id );
			if ($res) {
				// task created successfully
				return $new_task_id;
			} else {
				// task failed to create
				return NULL;
			}
		} else {
			// task failed to create
			return NULL;
		}
	}
	
	/**
	 * Fetching single task
	 *
	 * @param String $task_id
	 *        	id of the task
	 */
	public function getTask($task_id, $user_id) {
		$stmt = $this->conn->prepare ( "SELECT t.id, t.task, t.status, t.created_at from tasks t, user_tasks ut WHERE t.id = ? AND ut.task_id = t.id AND ut.user_id = ?" );
		$stmt->bind_param ( "ii", $task_id, $user_id );
		if ($stmt->execute ()) {
			$res = array ();
			$stmt->bind_result ( $id, $task, $status, $created_at );
			// TODO
			// $task = $stmt->get_result()->fetch_assoc();
			$stmt->fetch ();
			$res ["id"] = $id;
			$res ["task"] = $task;
			$res ["status"] = $status;
			$res ["created_at"] = $created_at;
			$stmt->close ();
			return $res;
		} else {
			return NULL;
		}
	}
	public function getTaskByID($task_id) {
		$stmt = $this->conn->prepare ( "SELECT id, task, status, created_at from tasks WHERE id = ?" );
		$stmt->bind_param ( "i", $task_id );
		if ($stmt->execute ()) {
			$res = array ();
			$stmt->bind_result ( $id, $task, $status, $created_at );
			// TODO
			// $task = $stmt->get_result()->fetch_assoc();
			$stmt->fetch ();
			$res ["id"] = $id;
			$res ["task"] = $task;
			$res ["status"] = $status;
			$res ["created_at"] = $created_at;
			$stmt->close ();
			return $res;
		} else {
			return NULL;
		}
	}
	
	/**
	 * fetching task by id and status
	 *
	 * @param unknown $userid        	
	 * @param unknown $status        	
	 * @return unknown
	 */
	public function getTasksByStatus($userid, $status) {
		$stmt = $this->conn->prepare ( "SELECT t.id FROM tasks t, user_tasks ut WHERE t.id = ut.task_id AND ut.user_id = ? AND t.status = ?" );
		$stmt->bind_param ( "ii", $userid, $status );
		$stmt->execute ();
		$tasks = $stmt->get_result ();
		$stmt->close ();
		return $tasks;
	}
	
	/**
	 * Fetching all user tasks
	 *
	 * @param String $user_id
	 *        	id of the user
	 */
	public function getAllUserTasks($user_id) {
		$stmt = $this->conn->prepare ( "SELECT t.* FROM tasks t, user_tasks ut WHERE t.id = ut.task_id AND ut.user_id = ?" );
		$stmt->bind_param ( "i", $user_id );
		$stmt->execute ();
		$tasks = $stmt->get_result ();
		$stmt->close ();
		return $tasks;
	}
	
	/**
	 * Fetching all tasks
	 */
	public function getAllTasks() {
		$stmt = $this->conn->prepare ( "SELECT t.*,ut.user_id FROM tasks t, user_tasks ut WHERE t.id = ut.task_id" );
		$stmt->execute ();
		$tasks = $stmt->get_result ();
		$stmt->close ();
		return $tasks;
	}
	
	/**
	 * Fetching all tasks create today
	 */
	public function getAllTasksToday() {
		$stmt = $this->conn->prepare ( "SELECT t.*,ut.user_id FROM tasks t, user_tasks ut WHERE t.id = ut.task_id AND t.created_at >= '".date("Y-m-d")."' ORDER BY t.status asc" );
		$stmt->execute ();
		$tasks = $stmt->get_result ();
		$stmt->close ();
		return $tasks;
	}
	
	/**
	 * Fetching all tasks order by date before today
	 */
	public function getAllTasksOrderByDate() {
		$stmt = $this->conn->prepare ( "SELECT t.*,ut.user_id FROM tasks t, user_tasks ut WHERE t.id = ut.task_id AND t.created_at < '".date("Y-m-d")."' ORDER BY t.status asc,t.created_at desc" );
		$stmt->execute ();
		$tasks = $stmt->get_result ();
		$stmt->close ();
		return $tasks;
	}
	/**
	 * Updating task
	 *
	 * @param String $task_id
	 *        	id of the task
	 * @param String $task
	 *        	task text
	 * @param String $status
	 *        	task status
	 */
	public function updateTask($user_id, $task_id, $task, $status) {
		$stmt = $this->conn->prepare ( "UPDATE tasks t, user_tasks ut set t.task = ?, t.status = ? WHERE t.id = ? AND t.id = ut.task_id AND ut.user_id = ?" );
		$stmt->bind_param ( "siii", $task, $status, $task_id, $user_id );
		$stmt->execute ();
		$num_affected_rows = $stmt->affected_rows;
		$stmt->close ();
		return $num_affected_rows > 0;
	}
	
	/**
	 * Updating task
	 *
	 * @param String $task_id
	 *        	id of the task
	 * @param String $status
	 *        	task status
	 */
	public function updateTaskStatus($task_id, $status) {
		$stmt = $this->conn->prepare ( "UPDATE tasks set status = ? WHERE id = ?" );
		$stmt->bind_param ( "ii", $status, $task_id );
		$stmt->execute ();
		$num_affected_rows = $stmt->affected_rows;
		$stmt->close ();
		return $num_affected_rows > 0;
	}
	
	/**
	 * Deleting a task
	 *
	 * @param String $task_id
	 *        	id of the task to delete
	 */
	public function deleteTask($user_id, $task_id) {
		$stmt = $this->conn->prepare ( "DELETE t FROM tasks t, user_tasks ut WHERE t.id = ? AND ut.task_id = t.id AND ut.user_id = ?" );
		$stmt->bind_param ( "ii", $task_id, $user_id );
		$stmt->execute ();
		$num_affected_rows = $stmt->affected_rows;
		$stmt->close ();
		return $num_affected_rows > 0;
	}
	
	/* ------------- `user_tasks` table method ------------------ */
	
	/**
	 * Function to assign a task to user
	 *
	 * @param String $user_id
	 *        	id of the user
	 * @param String $task_id
	 *        	id of the task
	 */
	public function createUserTask($user_id, $task_id) {
		$stmt = $this->conn->prepare ( "INSERT INTO user_tasks(user_id, task_id) values(?, ?)" );
		$stmt->bind_param ( "ii", $user_id, $task_id );
		$result = $stmt->execute ();
		
		if (false === $result) {
			die ( 'execute() failed: ' . htmlspecialchars ( $stmt->error ) );
		}
		$stmt->close ();
		return $result;
	}
	
	/* ------------- `projects` table method ------------------ */
	
	/**
	 * Function to add a project
	 *
	 * @param String $project_name
	 *        	name of project
	 * @param String $description
	 *        	intro about project
	 */
	public function createProject($project_name, $description) {
		$stmt = $this->conn->prepare ( "INSERT INTO projects(project_name, description) values(?, ?)" );
		$stmt->bind_param ( "ss", $project_name, $description );
		$result = $stmt->execute ();
		
		if (false === $result) {
			die ( 'execute() failed: ' . htmlspecialchars ( $stmt->error ) );
		}
		$stmt->close ();
		return $result;
	}
	
	/**
	 * update a project
	 *
	 * @param int $project_id        	
	 * @param String $project_name        	
	 * @param String $description        	
	 * @return boolean
	 */
	public function updateProject($project_id, $project_name, $description) {
		$stmt = $this->conn->prepare ( "UPDATE projects set project_name = ?, description = ? WHERE id = ?" );
		$stmt->bind_param ( "ssi", $project_name, $description, $project_id );
		$stmt->execute ();
		$num_affected_rows = $stmt->affected_rows;
		$stmt->close ();
		return $num_affected_rows > 0;
	}
	
	/**
	 * Fetching all project
	 */
	public function getAllProjects() {
		$stmt = $this->conn->prepare ( "SELECT * FROM projects" );
		$stmt->execute ();
		$projects = $stmt->get_result ();
		$stmt->close ();
		return $projects;
	}
	public function getProjectById($project_id) {
		$stmt = $this->conn->prepare ( "SELECT * from projects WHERE id = ?" );
		$stmt->bind_param ( "i", $project_id );
		if ($stmt->execute ()) {
			$res = array ();
			$stmt->bind_result ( $id, $project_name, $description );
			// TODO
			// $task = $stmt->get_result()->fetch_assoc();
			$stmt->fetch ();
			$res ["id"] = $id;
			$res ["project_name"] = $project_name;
			$res ["description"] = $description;
			$stmt->close ();
			return $res;
		} else {
			return NULL;
		}
	}
	
	/* ------------- `QAs` table method ------------------ */
	public function getAllQAs() {
		$stmt = $this->conn->prepare ( "SELECT * FROM QAs ORDER BY created_at DESC" );
		$stmt->execute ();
		$qas = $stmt->get_result ();
		$stmt->close ();
		return $qas;
	}
	public function createQA($title, $content, $project_id, $user_id) {
		$stmt = $this->conn->prepare ( "INSERT INTO QAs(title, content, project_id, user_id) values(?, ?, ?, ?)" );
		$stmt->bind_param ( "ssii", $title, $content, $project_id, $user_id );
		$result = $stmt->execute ();
		
		if (false === $result) {
			die ( 'execute() failed: ' . htmlspecialchars ( $stmt->error ) );
		}
		$stmt->close ();
		return $result;
	}
	public function getQAById($id) {
		$stmt = $this->conn->prepare ( "SELECT * from QAs WHERE id = ?" );
		$stmt->bind_param ( "i", $id );
		if ($stmt->execute ()) {
			$res = array ();
			$stmt->bind_result ( $qid, $title, $content, $status, $created_at, $project_id, $user_id , $answer);
			// TODO
			// $task = $stmt->get_result()->fetch_assoc();
			$stmt->fetch ();
			$res ["id"] = $qid;
			$res ["title"] = $title;
			$res ["content"] = $content;
			$res ["created_at"] = $created_at;
			$res ["project_id"] = $project_id;
			$res ["user_id"] = $user_id;
			$res ["answer"] = $answer;
			$stmt->close ();
			return $res;
		} else {
			return NULL;
		}
	}
	public function getQAByUser($user_id) {
		$stmt = $this->conn->prepare ( "SELECT * FROM QAs WHERE user_id=? ORDER BY created_at DESC" );
		$stmt->bind_param ( "i", $user_id );
		$stmt->execute ();
		$qas = $stmt->get_result ();
		$stmt->close ();
		return $qas;
	}
	public function updateQA($id, $title, $content, $status, $project_id) {
		$stmt = $this->conn->prepare ( "UPDATE QAs set title = ?, content = ?, project_id = ?, status = ? WHERE id = ?" );
		$stmt->bind_param ( "ssiii", $title, $content, $project_id, $status, $id );
		$stmt->execute ();
		$num_affected_rows = $stmt->affected_rows;
		$stmt->close ();
		return $num_affected_rows > 0;
	}
	public function updateQAStatus($id, $status) {
		$stmt = $this->conn->prepare ( "UPDATE QAs set status = ? WHERE id = ?" );
		$stmt->bind_param ( "ii", $status, $id );
		$stmt->execute ();
		$num_affected_rows = $stmt->affected_rows;
		$stmt->close ();
		return $num_affected_rows > 0;
	}
	
	public function updateQAAnswer($id, $answer) {
		$stmt = $this->conn->prepare ( "UPDATE QAs set answer = ? WHERE id = ?" );
		$stmt->bind_param ( "si", $answer, $id );
		$stmt->execute ();
		$num_affected_rows = $stmt->affected_rows;
		$stmt->close ();
		return $num_affected_rows > 0;
	}
	/* ------------- `references` table method ------------------ */
	
	public function getAllRefs() {
		$stmt = $this->conn->prepare ( "SELECT * FROM `references`" );
		$stmt->execute ();
		$refs = $stmt->get_result ();
		$stmt->close ();
		return $refs;
	}
	
	public function createRef($url, $des) {
		$stmt = $this->conn->prepare ( "INSERT INTO `references`(url,description) values(?, ?)" );
		$stmt->bind_param ( "ss", $url, $des );
		$result = $stmt->execute ();
	
		if (false === $result) {
			die ( 'execute() failed: ' . htmlspecialchars ( $stmt->error ) );
		}
		$stmt->close ();
		return $result;
	}
	
	public function updateRef($id, $url, $des) {
		$stmt = $this->conn->prepare ( "UPDATE `references` set url = ?, description = ? WHERE id = ?" );
		$stmt->bind_param ( "ssi", $url, $des, $id );
		$stmt->execute ();
		$num_affected_rows = $stmt->affected_rows;
		$stmt->close ();
		return $num_affected_rows > 0;
	}
}

?>
