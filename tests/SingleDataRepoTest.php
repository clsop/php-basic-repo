<?php
namespace base\data\test {
	use \PHPUnit\Framework\TestCase;

	class DataRepo implements \base\data\repository\IDataRepository {
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

		public function create($object): array {
			array_push($this->source, $object);
			return $this->source;
		}

		public function update($object): array {
			$this->source[$object] = $object;
			return $this->source;
		}

		public function delete($object) {
			$this->source[$object] = NULL;
		}

		public function deleteById($uid) {
			$this->source[$uid] = NULL;
		}
	}

	class SingleDataRepoTest extends TestCase {
		protected $dataRepo;

		protected function setUp() {
			$this->dataRepo = new DataRepo();
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

		public function testCreate() {
			$rnd = rand();
			$this->dataRepo->create('test' . $rnd);
			
			$this->assertEquals('test' . $rnd, $this->dataRepo->source[count($this->dataRepo->source) - 2]);
		}

		public function testUpdate() {
			$rnd = rand();
			$this->dataRepo->update('test' . $rnd);
			
			$this->assertEquals('test' . $rnd, $this->dataRepo->find('test' . $rnd));
		}

		public function testDelete() {
			$rnd = rand();
			$this->dataRepo->delete(1);
			
			$this->assertNull($this->dataRepo->findById(1));
		}

		public function testDeleteById() {
			$this->dataRepo->deleteById(0);

			$this->assertNull($this->dataRepo->findById(0));
		}
	}
}
?>