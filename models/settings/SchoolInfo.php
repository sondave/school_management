<?php

declare(strict_types=1);

namespace app\models\settings;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "st_school_info".
 *
 * @property int $id
 * @property string $name
 * @property string|null $website
 * @property string|null $email
 * @property string $phone_number
 * @property string $county
 * @property string $physical_address
 * @property string|null $postal_address
 * @property string $school_type
 * @property string|null $motto
 * @property string|null $mission
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class SchoolInfo extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'st_school_info';
    }


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_by'],
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['name', 'phone_number', 'county', 'physical_address', 'school_type'], 'required'],
            [['name'], 'validateSingleRecord'],
            [['mission'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'website', 'email', 'physical_address', 'postal_address', 'motto'], 'string', 'max' => 255],
            [['phone_number'], 'string', 'max' => 30],
            [['county', 'school_type'], 'string', 'max' => 100],
            [['school_type'], 'in', 'range' => array_keys(self::getSchoolTypeOptions())],
            [['county'], 'in', 'range' => array_keys(self::getKenyaCountyOptions())],
            [['email'], 'email'],
            [['website'], 'url', 'defaultScheme' => 'https'],
        ];
    }

    public static function getSchoolTypeOptions(): array
    {
        $options = [
            // 'Boarding' => 'Boarding',
            // 'College' => 'College',
            'Day' => 'Day',
            'Day_and_Boarding' => 'Day and Boarding',
            'Boarding' => 'Boarding',
            // 'Mixed Day and Boarding' => 'Mixed Day and Boarding',
            // 'Other' => 'Other',
            // 'Primary' => 'Primary',
            // 'Secondary' => 'Secondary',
            // 'Technical and Vocational' => 'Technical and Vocational',
            // 'Special Needs' => 'Special Needs',
            // 'University' => 'University',
        ];

        asort($options, SORT_NATURAL | SORT_FLAG_CASE);

        return $options;
    }

    public static function getKenyaCountyOptions(): array
    {
        $options = [
            'Baringo' => 'Baringo',
            'Bomet' => 'Bomet',
            'Bungoma' => 'Bungoma',
            'Busia' => 'Busia',
            'Elgeyo-Marakwet' => 'Elgeyo-Marakwet',
            'Embu' => 'Embu',
            'Garissa' => 'Garissa',
            'Homa Bay' => 'Homa Bay',
            'Isiolo' => 'Isiolo',
            'Kajiado' => 'Kajiado',
            'Kakamega' => 'Kakamega',
            'Kericho' => 'Kericho',
            'Kiambu' => 'Kiambu',
            'Kilifi' => 'Kilifi',
            'Kirinyaga' => 'Kirinyaga',
            'Kisii' => 'Kisii',
            'Kisumu' => 'Kisumu',
            'Kitui' => 'Kitui',
            'Kwale' => 'Kwale',
            'Laikipia' => 'Laikipia',
            'Lamu' => 'Lamu',
            'Machakos' => 'Machakos',
            'Makueni' => 'Makueni',
            'Mandera' => 'Mandera',
            'Marsabit' => 'Marsabit',
            'Meru' => 'Meru',
            'Migori' => 'Migori',
            'Mombasa' => 'Mombasa',
            'Nairobi' => 'Nairobi',
            'Nakuru' => 'Nakuru',
            'Nandi' => 'Nandi',
            'Narok' => 'Narok',
            'Nyamira' => 'Nyamira',
            'Nyandarua' => 'Nyandarua',
            'Nyeri' => 'Nyeri',
            'Muranga' => 'Muranga',
            'Samburu' => 'Samburu',
            'Siaya' => 'Siaya',
            'Taita-Taveta' => 'Taita-Taveta',
            'Tana River' => 'Tana River',
            'Tharaka-Nithi' => 'Tharaka-Nithi',
            'Trans Nzoia' => 'Trans Nzoia',
            'Turkana' => 'Turkana',
            'Uasin Gishu' => 'Uasin Gishu',
            'Vihiga' => 'Vihiga',
            'Wajir' => 'Wajir',
            'West Pokot' => 'West Pokot',
        ];

        asort($options, SORT_NATURAL | SORT_FLAG_CASE);

        return $options;
    }

    public function validateSingleRecord(string $attribute): void
    {
        $query = self::find();

        if (!$this->isNewRecord) {
            $query->andWhere(['<>', 'id', $this->id]);
        }

        if ($query->exists()) {
            $this->addError($attribute, 'Only one school information record is allowed. Please update the existing record.');
        }
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'website' => 'Website',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'county' => 'County',
            'physical_address' => 'Physical Address',
            'postal_address' => 'Postal Address',
            'school_type' => 'School Type',
            'motto' => 'Motto',
            'mission' => 'Mission',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
