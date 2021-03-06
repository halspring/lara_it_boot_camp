<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Logics\UserLogic;
use App\Http\Logics\UserGroupLogic;
use Request;
use Redirect;
use DB;

class UserController extends Controller
{
    private $userLogic;
    private $userGroupLogic;

    /**
     * コンストラクタ
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->userLogic = new UserLogic;
        $this->userGroupLogic = new UserGroupLogic;
    }

    /**
     * トップページ
     *
     */
    public function index($page = 1)
    {
        return $this->render(
            "admin/user/index",
            [
              "user_data_list" => $this->userLogic->getDataList(),
              "user_group_data_list" => $this->userGroupLogic->getDataList(),
            ]
        );
    }

    /**
     * 詳細ページ
     *
     */
    public function detail($user_id)
    {
        return $this->render(
            "admin/user/detail",
            [
                //最終ログインから現在までの経過日数
                "past_date" => $this->userLogic->getLastLoginTime($user_id),
                // ユーザー情報 (アクセスユーザのuser_dataと区別するために'account'を使用)
                "account_data" => $this->userLogic->getData($user_id),
                // ユーザーグループ情報
                "user_group_data_list" => $this->userGroupLogic->getDataList(),]
        );
    }

    /**
     * 入力内容確認
     *
     */
    public function confirm()
    {
        $input = Request::all();

        // 登録処理
        $this->userLogic->updateData(
            [
                [
                    "key" => "user.user_id",
                    "operator" => "=",
                    "value" => $input["user_id"],],],
            [
                "user_user_group_id" => $input["user_user_group_id"],
                "updated_at" => date("Y-m-d H:i:s"),]);

        return Redirect::to("/admin/user/" . $input["user_id"]);
    }


        public function delete($user_id = null) //ユーザー削除
    {
        $user_data = [];
        if (isset($user_id) && !empty($user_id)) {
          DB::delete('delete from User where user_id = ?',[$user_id]);
        }

        return Redirect::to("/admin/user_list/");
    }
}
