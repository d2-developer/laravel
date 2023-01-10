<?php 

public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Review::where(['domain_id' =>  getUserDomain()])->orderBy('position'); //->orderBy('id', 'desc');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    return   \Carbon\Carbon::parse($row->created_at)->format(dataFormat());
                })
                ->addColumn('featured_image', function ($row) {
                    $html = '';
                    if ($row->featured_image) {
                        $html .= '<img src="' . url($row->featured_image) . '" alt="" title="' . $row->name . '" class="rounded me-2" height="70" width="70">';
                    }
                    return $html;
                })
                ->addColumn('review_content', function ($row) {
                    return ($row->review_content) ? limit_text(strip_tags($row->review_content)) : $row->review_content;
                })
                ->addColumn('status', function ($row) {
                    $btn = '<span searchvalue="1" class="badge bg-success">' . __('Published') . '</span>';
                    if ($row->status == 0) {
                        $btn = '<span searchvalue="0" class="badge bg-danger">' . __('Unpublished') . '</span>';
                    }
                    return $btn;
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <a href="' . route('reviews.edit', [$row->id]) . '" class="action-icon"> <i class="mdi mdi-square-edit-outline"></i></a>
                    <a data-id="' . $row->id . '"  data-url="' . route('reviews.destroy', [$row->id]) . '" data-table="delete_item" href="javascript:void(0);" class="action-icon action-delete"> <i data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete" class="mdi mdi-delete"></i></a>';
                    return $btn;
                })
                ->rawColumns(['featured_image', 'review_content', 'status', 'action'])
                ->make(true);
        }
        $domain_id = getUserDomain();
        $tempData = ['section_status' => 0, 'text' => '', 'title' => '', 'menu' => ['status' => 1, 'title' => '',], 'about_buttun' => ['status' => 0, 'name' => '', 'link' => '']];
        $dbData = Content::where(['domain_id' => $domain_id, 'meta_key' => 'review_settings'])->get()->toArray();
        if (isset($dbData[0]) && isset($dbData[0]['meta_value'])) {
            $tempData = json_decode($dbData[0]['meta_value'], true);
        }
        return view('review.list', $tempData);
    }
?>
