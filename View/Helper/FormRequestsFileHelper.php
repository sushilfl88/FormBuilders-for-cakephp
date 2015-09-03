 <?php
class FormRequestsFileHelper extends AppHelper
{
    var $helpers = array('Session');
    
    /*Added on 11 July 2013
     * @getAssociatedFormFilesHtmlMetadata list uploaded files details
     * Show form requested
     * @param integer $fileId file id
     * @param integer $fileAssociation relation id
     * @version 5.2 
     * @ref REDDEV-4830
     * 
     * */
    function getAssociatedFormFilesHtmlMetadata($fileIds, $fileAssociation)
    {
        $fileData = $this->getAssociatedFormFilesById($fileIds);
        ob_start();
?>
                    <div class="uploaded-files fieldset_padding_bottom">
                       <ul>
                       <?php
        $url = Configure::read('url');
        
        foreach ($fileData as $fileDataVal):
            $filepath = $url . $fileDataVal['File']['path'] . '/' . $fileDataVal['File']['file_name'];
            if (file_exists($fileDataVal['File']['path'] . '/' . $fileDataVal['File']['file_name'])) {
                $js = "onclick=window.open('" . $filepath . "','','resizable=1');";?>
                           <li class="dropdown" id="delete-<?php   echo $fileDataVal['File']['id'];?>">
                             <a data-toggle="dropdown" class="dropdown-toggle uploaded-filename" href="#">
                             	<?php echo $fileDataVal['File']['label'];?><b class="caret"></b>
                             </a>
                             <ul class="dropdown-menu">
                              <li><a onclick="<?php echo $js;?>" href="javascript:void(0);">Open in New Window</a></li>
                              <li><a href="<?php echo '/files/files/download/' . $fileDataVal['File']['id'] . '/0';?>" title="<?php echo $fileDataVal['File']['label'];
?>" class="support-file-name">Download</a></li>
                              <li><a href="javascript:void(0);" class="formrequest-file" id="<?php echo $fileDataVal['File']['id'];
?>" path="<?php echo $fileDataVal['File']['path'] . '/' . $fileDataVal['File']['file_name'];?>" >Delete</a></li>
                             </ul>
                             </li>                          
                            <?php
            }
        endforeach;
?>
                      </ul>
                      </div>
         <?php
        return ob_get_clean();
    }
    /** This function will get the list of associated files for provided fileIds
    * @params array $fileIds
    * @returns array
    * @version 5.3
    * @ref REDDEV-4834 	
    */
    function getAssociatedFormFilesById($fileIds)
    {
        if ($fileIds) {
            $file = ClassRegistry::init("Files.File");
            return $file->find('all', array(
                'conditions' => array(
                    'id' => $fileIds
                )
            ));
        } else {
            return array();
        }
    }
    
} 