<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Group;
use App\Models\Tool;
use App\Models\Colour;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seed Groups
        $groups = [
            'Fulfillment',
            'Service Desk',
            'Infrastructure',
            'Software Engineering',
            'Dev Ops',
            'Resilience',
            'Management'
        ];

        $groupModels = [];
        foreach ($groups as $groupName) {
            $groupModels[$groupName] = Group::create(['groupname' => $groupName]);
        }

        // Seed Colours
        $colours = [
            ['colour' => 'Blue', 'hex_code' => '#003865'],
            ['colour' => 'Teal', 'hex_code' => '#005C8A'],
            ['colour' => 'Maroon', 'hex_code' => '#7D2239'],
            ['colour' => 'Purple', 'hex_code' => '#5B4D94'],
            ['colour' => 'Green', 'hex_code' => '#385A4F'],
            ['colour' => 'Gray', 'hex_code' => '#4F5961'],
        ];
        foreach ($colours as $colour) {
            Colour::create($colour);
        }

        // Seed User
        $user = User::create([
            'username' => 'rmi30m',
            'fullname' => 'Russell McInnes',
            'admin' => true,
        ]);

        // Assign group to user
        $user->groups()->attach($groupModels['Dev Ops']->id);

        // Seed Tools
        $tools = [
            [
                'name' => 'Snipe',
                'url' => 'https://snipe.cose.gla.ac.uk',
                'colour' => '#005C8A',
                'image' => null,
                'groups' => ['Software Engineering'],
            ],
            [
                'name' => 'Wiki',
                'url' => 'https://wiki.cose.gla.ac.uk/',
                'colour' => '#7D2239',
                'image' => null,
                'groups' => ['Software Engineering'],
            ],
            [
                'name' => 'Room Booking',
                'url' => 'https://napuka.dcs.gla.ac.uk/home.php',
                'colour' => '#5B4D94',
                'image' => null,
                'groups' => ['Infrastructure'],
            ],
            [
                'name' => '1Password',
                'url' => 'https://jameswattschoolofengineeringuniv.1password.com/app',
                'colour' => '#385A4F',
                'image' => null,
                'groups' => ['Software Engineering'],
            ],
            [
                'name' => 'Ivanti',
                'url' => 'https://glasgow.saasiteu.com/Default.aspx/',
                'colour' => '#4F5961',
                'image' => null,
                'groups' => ['Service Desk'],
            ],
            [
                'name' => 'Rubrik Backups',
                'url' => 'https://glasgow.my.rubrik.com/',
                'colour' => '#003865',
                'image' => null,
                'groups' => ['Infrastructure'],
            ],
            [
                'name' => 'PowerBI Reports',
                'url' => '#',
                'colour' => '#005C8A',
                'image' => null,
                'groups' => ['Management'],
            ],
            [
                'name' => 'Microsoft Intune Admin Center',
                'url' => '#',
                'colour' => '#7D2239',
                'image' => null,
                'groups' => ['Infrastructure'],
            ],
        ];

        foreach ($tools as $toolData) {
            $tool = Tool::create([
                'name' => $toolData['name'],
                'url' => $toolData['url'],
                'colour' => $toolData['colour'],
                'image' => $toolData['image'],
            ]);

            // Attach groups to the tool
            $groupIds = [];
            foreach ($toolData['groups'] as $groupName) {
                if (isset($groupModels[$groupName])) {
                    $groupIds[] = $groupModels[$groupName]->id;
                }
            }
            $tool->groups()->attach($groupIds);
        }
    }
}
