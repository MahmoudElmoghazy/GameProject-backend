<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories=[
            'ثقافة عامة',
            'تاريخ',
            'رياضة',
            'حساب',
            'علوم',
            'جغرافيا',
            'اسلامية',
            'ألغاز'
        ];
        foreach ($categories as $category){
            Category::create(['name'=>$category]);
        }
        $questions=[
            'متى نشأت الأمم المتحدة؟'=>[
                '1945',
                '1946',
                '1947',
                '1948'
            ],
            'متى انهار الاتحاد السوفيتي؟'=>[
                '1990',
                '1991',
                '1992',
                '1993'
            ],
            'لاعب إسباني حصل على الدوري الإنجليزي مرتين، واختتم حياته الكروية في كومو الإيطالي
           رقم تكرره 50 مرة لتحصل على 50000. ما هو؟'
            =>['ميشيل سالغادو','فرناندو توريس','راؤول','ديفيد فيا'],
        ];
        foreach ($questions as $question=>$answers){
            $category=Category::where('name','ثقافة عامة')->first();
            $question=$category->questions()->create(['title'=>$question,'difficulty_id'=>1,'type'=>'mcq']);
            foreach ($answers as $answer){
                $question->answers()->create(['title'=>$answer]);
            }
            $question->update(['right_answer_id'=>$question->answers()->first()->id]);
        }
        $categories= Category::all();
        foreach ($categories as $category ){
            foreach ($questions as $question=>$answers){
                $question=$category->questions()->create(['title'=>$question,'difficulty_id'=>1,'right_answer_id'=>1,'type'=>'mcq']);
            foreach ($answers as $answer){
                $question->answers()->create(['title'=>$answer]);
            }
                $question->update(['right_answer_id'=>$question->answers()->first()->id]);
            }
        }

        Setting::create(['key'=>'coins_per_game','value'=>100]);
    }
}
