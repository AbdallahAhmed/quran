<?php//

namespace App;

use Illuminate\Database\Eloquent\Model;

class Not extends Model
{
    protected $table = "notifications";

    public function getMessageAttribute(){
        $type = $this->type;
        if($type == "new_contest_content"){
            return trans('app.'.$type);
        }elseif ($type == "new_member_join_contest"){
            return str_replace(":name", $this->from_id, trans('app.'.$type));
        }
    }
}
