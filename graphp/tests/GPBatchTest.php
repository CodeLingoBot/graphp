<?

class GPTestBatchModel extends GPNode {

  public static $customDelete = false;

  protected static function getDataTypesImpl() {
    return [
      new GPDataType('name', GPDataType::GP_STRING, true),
      new GPDataType('age', GPDataType::GP_INT),
    ];
  }

  protected static function getEdgeTypesImpl() {
    return [
      new GPEdgeType(GPTestBatchModel2::class),
    ];
  }

  public function delete() {
    parent::delete();
    self::$customDelete = true;
  }
}

class GPTestBatchModel2 extends GPNode {
  protected static function getEdgeTypesImpl() {
    return [
      new GPEdgeType(GPTestBatchModel3::class),
      new GPEdgeType(GPTestBatchModel4::class),
    ];
  }
}

class GPTestBatchModel3 extends GPNode {}
class GPTestBatchModel4 extends GPNode {}

class GPBatchTest extends GPTest {

  public static function setUpBeforeClass() {
    GPDatabase::get()->beginUnguardedWrites();
    GPNodeMap::addToMapForTest(GPTestBatchModel::class);
    GPNodeMap::addToMapForTest(GPTestBatchModel2::class);
    GPNodeMap::addToMapForTest(GPTestBatchModel3::class);
    GPNodeMap::addToMapForTest(GPTestBatchModel4::class);
  }

  public function testBatchSave() {
    $m1 = new GPTestBatchModel();
    $m2 = new GPTestBatchModel();
    GPNode::batchSave([$m1, $m2]);
    $this->assertNotEmpty($m1->getID());
    $this->assertNotEmpty($m2->getID());
  }

  public function testBatchDelete() {
    GPTestBatchModel::$customDelete = false;
    $m1 = new GPTestBatchModel();
    $m2 = new GPTestBatchModel();
    GPNode::batchSave([$m1, $m2]);
    $this->assertNotEmpty($m1->getID());
    $this->assertNotEmpty($m2->getID());
    GPNode::batchDelete([$m1, $m2]);
    $results = GPNode::multiGetByID([$m1->getID(), $m2->getID()]);
    $this->assertEmpty($results);
    $this->assertTrue(GPTestBatchModel::$customDelete);
  }

  public function testSimpleBatchDelete() {
    GPTestBatchModel::$customDelete = false;
    $m1 = new GPTestBatchModel();
    $m2 = new GPTestBatchModel();
    GPNode::batchSave([$m1, $m2]);
    $this->assertNotEmpty($m1->getID());
    $this->assertNotEmpty($m2->getID());
    GPNode::simpleBatchDelete([$m1, $m2]);
    $results = GPNode::multiGetByID([$m1->getID(), $m2->getID()]);
    $this->assertEmpty($results);
    $this->assertFalse(GPTestBatchModel::$customDelete);
  }

  public function testBatchLoad() {
    $m1 = new GPTestBatchModel();
    $m2 = new GPTestBatchModel2();
    $m3 = new GPTestBatchModel3();
    $m4 = new GPTestBatchModel4();
    GPNode::batchSave([$m1, $m2, $m3, $m4]);
    $m1->addGPTestBatchModel2($m2);
    $m2->addGPTestBatchModel3($m3);
    $m2->addGPTestBatchModel4($m4);
    GPNode::batchSave([$m1, $m2]);
    GPNode::batchLoadConnectedNodes(
      [$m1, $m2, $m3],
      array_merge(
        GPTestBatchModel::getEdgeTypes(),
        GPTestBatchModel2::getEdgeTypes()
      )
    );
    $this->assertNotEmpty($m1->getGPTestBatchModel2());
    $this->assertNotEmpty($m2->getGPTestBatchModel3());
    $this->assertNotEmpty($m2->getGPTestBatchModel4());
  }

  public static function tearDownAfterClass() {
    GPNode::simpleBatchDelete(GPTestBatchModel::getAll());
    GPNode::simpleBatchDelete(GPTestBatchModel2::getAll());
    GPNode::simpleBatchDelete(GPTestBatchModel3::getAll());
    GPNode::simpleBatchDelete(GPTestBatchModel4::getAll());
    GPDatabase::get()->endUnguardedWrites();
  }

}
