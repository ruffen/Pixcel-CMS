<?php
class PageRepository extends MysqlDb{
	private $pageCache = array();
	public function getPage($id){
		global $dRep;
		if(isset($this->pageCache[$id])){
			return $this->pageCache[$id];
		}
		$sql = "SELECT * FROM ink_pages A
				WHERE A.pageId = '{$id}' AND A.currentRevision = 1";
		$row = $this->runSingleQuery($sql);
		$properties = array(
					'id' => $row['pageId'],
					'revisionId' => $row['revisionId'],
					'parent' => $row['parentId'],
					'order' => $row['pageOrder'],
					'author' => $dRep->getUser($row['authorId']),
					'template' => $dRep->getTemplate($row['templateId']),
					'created' => $row['dateCreated'],
					'published' => $row['published'],
					'publishedDate' => $row['pubDate'],
					'revisions' => $this->getPagerevisions($row['pageId']),
					'siteId' => $row['siteId'],
					'index' => $row['indexpage']
				);			
		$page = new Page();
		$page->setProperties($properties);
		$page = $this->getPageLanguage($page);
		$page = $this->getSpotvalues($page);
		$this->pageCache[$id] = $page;
		return $page;
	}
	public function getPageCollection($where, $order, $limit){
		global $dRep;
		$selectorSql = (isset($where['published'])) ? '' : "AND currentRevision = 1";
		$where = $this->sqlBuilder->createWhere($where, '');
		$limit = ($limit !== null && is_int($limit)) ? 'LIMIT '.$limit : '';
		$order = ($limit !== null && is_string($order)) ? $order : 'pageOrder';
		
		$sql = "SELECT * FROM ink_pages WHERE 
				{$where} {$selectorSql} ORDER BY '{$order}' ASC {$limit};";
		$data = $this->runManyQuery($sql);
		$pages = array();
		foreach($data as $index => $row){
			if(!empty($row['pageId'])){
				$properties = array(
					'id' => $row['pageId'],
					'revisionId' => $row['revisionId'],
					'parent' => $row['parentId'],
					'order' => $row['pageOrder'],
					'author' => $dRep->getUser($row['authorId']),
					'template' => $dRep->getTemplate($row['templateId']),
					'created' => $row['dateCreated'],
					'published' => $row['published'],
					'publishedDate' => $row['pubDate'],
					'revisions' => $this->getPagerevisions($row['pageId']),
					'siteId' => $row['siteId'],
					'index' => $row['indexpage']
					);			
				$page = new Page();
				$page->setProperties($properties);
				$page = $this->getPageLanguage($page);
				$page = $this->getSpotvalues($page);
				$this->pageCache[$row['pageId']] = $page;
				$pages[] = $page;
			}
		}
		return $pages;
	}
	private function getSpotvalues($page){
		$pageId = (is_object($page)) ? $page->getId() : $page;
		$sql = "SELECT A.* FROM ink_pages_spot_value A
				INNER JOIN ink_pages B ON (A.pageId = B.pageId AND B.revisionId = A.revisionId)
				WHERE A.pageId = '{$pageId}' AND B.currentRevision = 1";
		$data = $this->runManyQuery($sql);
		foreach($data as $index => $row){
			$page->setLang($row['languageId'], $row['tplSpotId'], $row['value']);	
		}
		return $page;
	}
	public function getPageLanguage($page){
		$pageId = (is_object($page)) ? $page->getId() : $page;
		$sql = "SELECT A.* FROM ink_pages_languages A
				INNER JOIN ink_pages B ON (A.pageId = B.pageId AND B.revisionId = A.revisionId)
				WHERE A.pageId = '{$pageId}' AND B.currentRevision = 1";
		$data = $this->runManyQuery($sql);
		foreach($data as $index => $row){
			$page->setLang($row['languageId'], 'title', $row['title']);
			$page->setLang($row['languageId'], 'description', $row['meta_description']);
			$page->setLang($row['languageId'], 'keywords', $row['meta_keywords']);
		}		
		return $page;
	}
	private function savePageLanguage($page, $revisionNumber, $customer){
		$lang = $page->getLang();
		//update or insert new text
		foreach($lang as $langId => $texts){
			$sql = 	"INSERT INTO ink_pages_languages VALUES 
					('{$page->getId()}', 
					'{$langId}', 
					'{$revisionNumber}', 
					'{$texts->title}',
					'{$texts->description}',
					'{$texts->keywords}'
					);";
			$this->insertValues($sql);
		}		
	}
	public function savePage($page){
		//get revisions to make sure we have the latest information
		$customer = unserialize($_SESSION['customer']);
		if($page->getId() != 'new'){
			$revisionNumber = $page->nextRevision();
			$sql = "UPDATE ink_pages SET currentRevision = '0' WHERE pageId = '{$page->getId()}';";
			$this->updateRow($sql);
			$id = $page->getId();
		}else{
			//set page to draft. 		
			$page->setPublished('draft');
			$pages = $this->getPageCollection(array('parent' => 0), null, null);
			$order = count($pages);
			
			$revisionNumber = 1;
			$id = uniqid();
			$page->setProperties(array('id' => $id, 'order' => $order));
		}
		$pubDate = 0;
		$currenRevision = 1;
		$sql = "INSERT INTO ink_pages VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
		$values = array($id, $revisionNumber, $customer->getSite()->getId(), $page->getTemplate()->getId(), 
			$page->getAuthor()->getId(), $page->published(), $pubDate, time(), $page->getParent(), $page->getOrder(), $page->isIndex(), $currenRevision);
		$this->insertValues($sql, $values);
		//save the texts associated with the page
		$this->savePageLanguage($page, $revisionNumber, $customer);
		//save spot values
		$this->saveSpotValues($page, $revisionNumber, $customer);
		//clear the session, if there is any
		unset($_SESSION['Page'][$id]);
		if(isset($this->pageCache[$id])){
			unset($this->pageCache[$id]);		
		}		
		return $id;	
	}
	public function updatePage($page, $fieldName = null, $fieldValue = null){
		if($fieldName == null && $fieldValue == null){
			return $this->savePage($page);
		}
		$updateArray = array($fieldName => $fieldValue);
		$sqlNames = array(
			'published' => 'published',
			'order' => 'pageOrder',
			'parent'=> 'parentId',
			'index' => 'indexpage'
		);
		$fieldValue = (is_object($fieldValue)) ? $fieldValue->getId() : $fieldValue;
		$revision = (is_object($page->currentRevision())) ? $page->currentRevision()->getId() : 1;

		//if we are publishing a page, witdraw previously published page
		if($fieldName == 'published' && $fieldValue == 1){
			$time = time();
			$sqlUnPub = "UPDATE ink_pages set published = '4', pubDate = {$time} WHERE pageId = '{$page->getId()}' AND published = '1';";
			$sqlPubDate = "UPDATE ink_pages set pubDate = {$time} WHERE pageId = '{$page->getId()}' AND revisionId = {$revision};";
			//run both queries on one, they both need to be finished with a semicolon to make this work! 
			$this->updateRow($sqlUnPub.$sqlPubDate);
			$updateArray['publishedDate'] = $time;
		}else if($fieldName == 'index'){
			$sql = "UPDATE ink_pages SET indexpage = 0 WHERE siteId = ? AND indexpage = 1;";
			$this->updateRow($sql, array($page->getSiteId()));
		}
		$sql = "UPDATE ink_pages SET {$sqlNames[$fieldName]} = '{$fieldValue}' WHERE pageId = '{$page->getId()}' AND revisionId = {$revision}";
		$this->updateRow($sql);
		//make sure page object is updated and cache it
		$page->setProperties($updateArray);
		unset($this->pageCache[$page->getId()]);
	}
	private function saveSpotValues($page, $revisionNumber, $customer){
		$spots = $page->getTemplate()->getSpots();
		
		foreach($spots as $tplSpotId => $spot){
			if(get_parent_class($spot) !== 'AdminSpot'){
				continue;
			}
			try{
				$values = $page->getValue($tplSpotId, true);
				foreach($values as $langId => $value){
					$sql = "INSERT INTO ink_pages_spot_value (pageId, revisionId, tplSpotId, languageId, value) VALUES 
							('{$page->getId()}',
							'{$revisionNumber}',
							'{$tplSpotId}',
							'{$langId}',
							'{$value}');";
					$this->insertValues($sql);
				}
			}catch(DataException $e){}
		}
	}
	public function deletePage($page, $revision){
		global $dRep, $varChecker;
		$subPages = $this->getPageCollection(array('parent' => $page->getId()), 'order', '1');
		if(count($subPages) > 0){
			throw new ChildError("page_childerror");
		}
		if($revision !== null){
			$revision = (is_object($revision)) ? $revision->getId() : $revision;		
		}
		return $this->deleteRevision($page->getId(), $revision);
	}
	public function changeRevision($page, $revisionId){
		$sql = "UPDATE ink_pages SET currentRevision = 0 WHERE pageId = '{$page->getId()}' AND currentRevision = 1;";
		$this->updateRow($sql);
		$sql = "UPDATE ink_pages SET currentRevision = 1 WHERE pageId = '{$page->getId()}' AND revisionId = {$revisionId}";
		$this->updateRow($sql);
		if(isset($this->pageCache[$page->getId()])){
			unset($this->pageCache[$page->getId()]);
		}
		$page = $this->getPage($page->getId());
		$this->pageCache[$page->getId()] = $page;
		return $this->getPage($page->getId());		
	}
	private function deleteRevision($pageId, $revisionId = null){
		$revisionSql = ($revisionId !== null) ? " AND revisionId = {$revisionId}" : '';
		//delete all text entries
		$sqlPageLang = "DELETE FROM ink_pages_languages WHERE pageId = '{$pageId}'{$revisionSql};";
		$this->deleteValues($sqlPageLang);
		//delete all spot values
		$sqlPageSpots = "DELETE FROM ink_pages_spot_value WHERE pageId = '{$pageId}'{$revisionSql};";
		$this->deleteValues($sqlPageSpots);
		//delete page
		$sqlPage = "DELETE FROM ink_pages WHERE pageId = '{$pageId}'{$revisionSql};";
		$this->deleteValues($sqlPage);
	}
	private function getPagerevisions($page){
		global $dRep;
		$pageId = (is_object($page)) ? $page->getId() : $page;

		$sql = "SELECT pageId, revisionId, authorId, dateCreated, currentRevision, published FROM ink_pages WHERE pageId = '{$pageId}' ORDER BY dateCreated DESC";
		$data = $this->runManyQuery($sql);
		$revisions = array();
		foreach($data as $index => $row){
			$revision = new PageRevision();
			$properties = array(
				'id' => $row['revisionId'],
				'author' => $dRep->getUser($row['authorId']),
				'timestamp' => $row['dateCreated'],
				'current' => $row['currentRevision'],
				'published' => $row['published']
			);
			$revision->setProperties($properties);
			$revisions[] = $revision;
		}
		return $revisions;
	}

}

?>