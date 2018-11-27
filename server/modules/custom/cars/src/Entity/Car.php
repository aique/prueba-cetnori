<?php

namespace Drupal\cars\Entity;

use Drupal\cars\Plugin\rest\resource\CarsResource;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\file\Entity\File;
use Drupal\user\UserInterface;

/**
 * Defines the Car entity.
 *
 * @ingroup cars
 *
 * @ContentEntityType(
 *   id = "car",
 *   label = @Translation("Car"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\cars\CarListBuilder",
 *     "views_data" = "Drupal\cars\Entity\CarViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\cars\Form\CarForm",
 *       "add" = "Drupal\cars\Form\CarForm",
 *       "edit" = "Drupal\cars\Form\CarForm",
 *       "delete" = "Drupal\cars\Form\CarDeleteForm",
 *     },
 *     "access" = "Drupal\cars\CarAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\cars\CarHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "car",
 *   admin_permission = "administer car entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "plate",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/car/{car}",
 *     "add-form" = "/admin/structure/car/add",
 *     "edit-form" = "/admin/structure/car/{car}/edit",
 *     "delete-form" = "/admin/structure/car/{car}/delete",
 *     "collection" = "/admin/structure/car",
 *   },
 *   field_ui_base_route = "car.settings"
 * )
 */
class Car extends ContentEntityBase implements CarInterface, \JsonSerializable
{

    use EntityChangedTrait;

    /**
     * {@inheritdoc}
     */
    public static function preCreate(EntityStorageInterface $storage_controller, array &$values)
    {
        parent::preCreate($storage_controller, $values);
        $values += [
            'user_id' => \Drupal::currentUser()->id(),
        ];
    }

    public static function postDelete(EntityStorageInterface $storage, array $entities)
    {
        parent::postDelete($storage, $entities);
        \Drupal::cache()->delete(CarsResource::CARS_LIST_CACHE_KEY); // limpia caché al eliminar un elemento
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedTime()
    {
        return $this->get('created')->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedTime($timestamp)
    {
        $this->set('created', $timestamp);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner()
    {
        return $this->get('user_id')->entity;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwnerId()
    {
        return $this->get('user_id')->target_id;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwnerId($uid)
    {
        $this->set('user_id', $uid);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwner(UserInterface $account)
    {
        $this->set('user_id', $account->id());
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isPublished()
    {
        return (bool)$this->getEntityKey('status');
    }

    /**
     * {@inheritdoc}
     */
    public function setPublished($published)
    {
        $this->set('status', $published ? TRUE : FALSE);
        return $this;
    }

    public function getPlate()
    {
        return $this->get('plate')->value;
    }

    public function getColor()
    {
        return $this->get('color')->value;
    }

    public function getKilometers()
    {
        return $this->get('kilometers')->value;
    }

    public function getPicture()
    {
        $picture = $this->get('picture')->getValue();

        if (!empty($picture[0]['target_id'])) {
            $file = File::load($picture[0]['target_id']);

            return file_create_url($file->getFileUri());
        }

        return $this->get('picture')->getValue();
    }

    public function setChangedTime($timestamp)
    {
        $this->set('changed', $timestamp);
        \Drupal::cache()->delete(CarsResource::CARS_LIST_CACHE_KEY); // limpia caché al crear/editar un elemento
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields = parent::baseFieldDefinitions($entity_type);

        $fields['plate'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Plate number'))
            ->addConstraint('UniqueField')
            ->setSetting('max_length', 8)
            ->setPropertyConstraints('value', array(
                'Regex' => array(
                    'pattern' => '/^[0-9]{4} [A-Z]{3}$/',
                    'message' => 'Invalid format, you must enter 4 digits followed by blank space followed by 3 uppercase chars.',
                ),
            ))
            ->setDisplayOptions('view', [
                'label' => 'hidden',
                'type' => 'string',
                'weight' => 1,
            ])
            ->setDisplayOptions('form', [
                'type' => 'string',
                'weight' => 1,
                'settings' => [
                    'size' => '8',
                    'placeholder' => '',
                ],
            ])
            ->setTranslatable(false)
            ->setRevisionable(false)
            ->setRequired(true);

        $fields['color'] = BaseFieldDefinition::create('list_string')// TODO reducir el tamaño del campo en base de datos para un campo list_string
        ->setLabel(t('Color'))
            ->setSettings([
                'allowed_values' => ['white' => 'White', 'gray' => 'Gray', 'red' => 'Red'],
            ])
            ->setDisplayOptions('view', [
                'label' => 'color',
                'type' => 'string',
                'weight' => 2,
            ])
            ->setDisplayOptions('form', [
                'type' => 'list_string',
                'weight' => 2,
            ])
            ->setDefaultValue('white')
            ->setTranslatable(false)
            ->setRevisionable(false)
            ->setRequired(true);

        $fields['kilometers'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Kilometers'))
            ->setSetting('min', 0)
            ->setDisplayOptions('view', [
                'label' => 'kilometers',
                'type' => 'integer',
                'weight' => 3,
            ])
            ->setDisplayOptions('form', [
                'type' => 'integer',
                'weight' => 3,
            ])
            ->setDefaultValue(0)
            ->setTranslatable(false)
            ->setRevisionable(false)
            ->setRequired(true);

        $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
            ->setLabel(t('Owner'))
            ->setSettings([
                'target_type' => 'user',
                'handler' => 'default',
            ])
            ->setDisplayOptions('view', [
                'label' => 'hidden',
                'type' => 'author',
                'weight' => 4,
            ])
            ->setDisplayOptions('form', [
                'type' => 'entity_reference_autocomplete',
                'weight' => 4,
                'settings' => [
                    'match_operator' => 'CONTAINS',
                    'size' => '60',
                    'autocomplete_type' => 'tags',
                    'placeholder' => '',
                ],
            ])
            ->setTranslatable(false)
            ->setRevisionable(false)
            ->setRequired(true);

        $fields['picture'] = BaseFieldDefinition::create('image')
            ->setLabel(t('Picture'))
            ->setSettings([
                'file_directory' => 'img',
                'alt_field_required' => false,
                'file_extensions' => 'png jpg jpeg',
            ])
            ->setDisplayOptions('view', [
                'label' => 'hidden',
                'type' => 'image',
                'weight' => 5,
            ])
            ->setDisplayOptions('form', [
                'type' => 'image',
                'weight' => 5,
            ])
            ->setTranslatable(false)
            ->setRevisionable(false)
            ->setRequired(true);

        $fields['status'] = BaseFieldDefinition::create('boolean')
            ->setLabel(t('Publishing status'))
            ->setDescription(t('A boolean indicating whether the Car is published.'))
            ->setDefaultValue(TRUE)
            ->setDisplayOptions('form', [
                'type' => 'boolean_checkbox',
                'weight' => -3,
            ]);

        $fields['created'] = BaseFieldDefinition::create('created')
            ->setLabel(t('Created'))
            ->setDescription(t('The time that the entity was created.'));

        $fields['changed'] = BaseFieldDefinition::create('changed')
            ->setLabel(t('Changed'))
            ->setDescription(t('The time that the entity was last edited.'));

        return $fields;
    }

    public function jsonSerialize()
    {
        return [
            'plate' => $this->getPlate(),
            'color' => ucfirst($this->getColor()),
            'kilometers' => number_format($this->getKilometers(), 0, ',', '.'),
            'owner' => $this->getOwner()->getDisplayName(),
            'picture' => $this->getPicture(),
        ];
    }

}
