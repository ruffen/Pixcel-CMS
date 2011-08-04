CREATE TRIGGER insertfolder BEFORE INSERT ON ink_folders
FOR EACH ROW SET new.folderOrder = (SELECT count(*) FROM ink_folders WHERE parentId = new.parentId AND siteId = new.siteId)


//not working in current versions of mysql! 
CREATE TRIGGER deletefolder AFTER DELETE ON ink_folders
FOR EACH ROW
UPDATE ink_folders SET folderOrder = folderOrder - 1 WHERE folderId IN (
SELECT folderId FROM (
SELECT folderId FROM ink_folders WHERE siteId = OLD.siteId AND parentId = OLD.parentId AND folderOrder > OLD.folderOrder
) AS x
)