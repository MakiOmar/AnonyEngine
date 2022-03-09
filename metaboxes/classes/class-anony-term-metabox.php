<?php
if( ! class_exists( 'ANONY_Term_Metabox' )){
 
 class ANONY_Term_Metabox extends ANONY_Meta_Box{

  /**
   * Constructor
   */ 
  public function __construct($meta_box = array()){
    
   if(empty($meta_box) || !is_array($meta_box)) return;

   $this->metabox = $meta_box;

   //Set metabox's data
   $this->setMetaboxData($this->metabox);

    add_action( 'init', array($this, 'registerTermMetaKey' ));
    add_action( $this->taxonomy .'_add_form_fields', array($this, 'addFormFields' ) );
    add_action( $this->taxonomy .'_edit_form_fields', array($this, 'editFormFields' ) );
    add_action( 'edited_'.$this->taxonomy, array($this, 'saveTermMeta'));  
    add_action( 'create_'.$this->taxonomy, array($this, 'saveTermMeta') );
  }

  /**
   * Set metabox data
   */
  public function setMetaboxData($metabox){
   $this->id = $metabox['id'];
   $this->taxonomy = $metabox['taxonomy'];
   $this->fields = $metabox['fields'];
   $this->context = $metabox['context'];
  }
  
  /**
   * Save term meta value
   */
  public function saveTermMeta($term_id){
    if ( ! isset( $_POST[$this->id.'_nonce'] ) || ! wp_verify_nonce( $_POST[$this->id.'_nonce'], $this->id.'_action' ) )
        return;
      
    $old_value = get_term_meta( $term_id, $this->id , true );
    
    if($old_value === $_POST[$this->id]) return;
    
    $term_value[$this->id] = $_POST[$this->id];
    
    if ($old_value && '' === $_POST[$this->id])
      delete_term_meta( $term_id, $this->id );
    
    else if ( $old_value !== $_POST[$this->id] )
      update_term_meta( $term_id, $this->id , $term_value );
    
  }

  /**
   * Register term meta key
   */
  public function registerTermMetaKey(){
   register_meta( 'term', $this->id , array($this, 'validateTermMeta' ) );
  }

  /**
   * validate metabox
   */
  public function validateTermMeta($value){
    
    

   return $value;
  }

  /**
   * Add metabox on add term page
   */
  public function addFormFields(){
    if ( $GLOBALS['pagenow'] !== 'edit-tags.php') return ;
    echo '<div class="form-field anony-term-meta-wrap">';
    echo $this->metaFieldsCallback();
    echo '</div>';
  }

  /**
   * Add metabox on edit term page
   */
  public function editFormFields($term){
    if ( $GLOBALS['pagenow'] !== 'term.php') return ;
   echo '<div class="form-field anony-term-meta-wrap">';
    echo $this->metaFieldsCallback($term->term_id);
    echo '</div>';
  }

  /**
   * Render metabox' fields.
   */
  public function metaFieldsCallback($object_id = null){

   if(!class_exists('ANONY_Input_Field')){
      esc_html_e( 'Input fields plugin is required', 'anonyengine' );
      return;
   }

   wp_nonce_field( $this->id.'_action', $this->id.'_nonce', false );
   
   //Loop through inputs to render
   foreach($this->fields as $field){    
    if (!is_null($object_id)) {
      $render_field = new ANONY_Input_Field($field, $this->id, 'term', intval($object_id));
    }else{
      $render_field = new ANONY_Input_Field($field, $this->id, 'term'); 
    }
   
    echo $render_field->field_init();

    $this->enqueueFieldScripts($field);
    
   }
  }
 }
}