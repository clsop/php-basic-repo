<?php
namespace base\data\test {
	use \PHPUnit\Framework\TestCase;

	class BatchDataRepo implements \base\data\repository\IBatchRepository {
		private $source;

		public function __construct() {
			$this->source = [10,20,30,40,50, 'test' => [0,1,2]];
		}

		public function __get($prop) {
			if (property_exists($this, $prop) && $prop == 'source') {
				return $this->$prop;
			}
		}

		public function findById($uid) {
			return $this->source[$uid];
		}

		public function find($object) {
			return $this->source[$object];
		}

		public function getAll() {
			return $this->source;
		}

		public function batchTransaction($objects): array {
			$updates = [];

			foreach ($objects['creates'] as $key => $value) {
				array_push($this->source, $value);
			}

			foreach ($objects['updates'] as $key => $value) {
				$oldValue = $this->source[$key];
				array_push($updates, $oldValue);
				$this->source[$key] = $value;
			}
			
			foreach ($objects['deletes'] as $key => $value) {
				$this->source[$value] = NULL;
			}

			return $updates;
		}

		public function deleteById($uid) {
			$this->source[$uid] = NULL;
		}
	}

	class BatchDataRepoTest extends TestCase {
		protected $dataRepo;

		protected function setUp() {
			$this->dataRepo = new BatchDataRepo();
		}

		public function testFindById() {
			$value = $this->dataRepo->findById(2);
			$this->assertEquals(30, $value);
		}

		public function testFind() {
			$value = $this->dataRepo->find('test');
			$this->assertEquals(3, count($value));
		}

		public function testGetAll() {
			$value = $this->dataRepo->getAll();
			$this->assertEquals(count($this->dataRepo->source), count($value));
		}

		public function testBatchTransaction() {
			$creates = [15, 21];
			$updates = [4 => 23, 5 => 16];
			$deletes = [0];
			$values = ['creates' => $creates, 'updates' => $updates, 'deletes' => $deletes];

			$oldUpdates = $this->dataRepo->batchTransaction($values);

			// could test values, for now only match size
			$this->assertEquals(count($updates), count($oldUpdates));
		}

		public function testDeleteById() {
			$this->dataRepo->deleteById(0);

			$this->assertNull($this->dataRepo->findById(0));
		}
	}
}
?>