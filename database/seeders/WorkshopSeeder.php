<?php

namespace Database\Seeders;

use App\Models\Workshop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class WorkshopSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'title' => 'Full-Stack MERN Development Workshop',
                'description' => 'Hands-on masterclass building responsive full-stack applications with MongoDB, Express.js, React, and Node.js. Learn modern RESTful APIs, JWT authentication, state management, and real-time features.',
                'scheduled_at' => Carbon::now()->addDays(5)->setHour(18)->setMinute(0),
                'capacity' => 15,
                'status' => 'published',
            ],
            [
                'title' => 'Data Analyst Bootcamp: Python, SQL & PowerBI',
                'description' => 'Extract, clean, and visualize data using Python (Pandas, NumPy), complex SQL queries, and interactive PowerBI dashboards. Perfect for translating raw numbers into actionable business insights.',
                'scheduled_at' => Carbon::now()->addDays(9)->setHour(14)->setMinute(30),
                'capacity' => 20,
                'status' => 'published',
            ],
            [
                'title' => 'Modern PHP & Laravel Architecture',
                'description' => 'Deep dive into Laravel 11 essentials: Service layers, Eloquent relationships, event-driven design, queues, and writing clean, scalable, and testable enterprise PHP code.',
                'scheduled_at' => Carbon::now()->addDays(14)->setHour(19)->setMinute(0),
                'capacity' => 12,
                'status' => 'published',
            ],
            [
                'title' => 'Cloud & DevOps with Docker and Kubernetes',
                'description' => 'Learn containerization and orchestration fundamentals using Docker and Kubernetes. Covers container lifecycle, microservices networking, Helm charts, and CI/CD deployment pipelines.',
                'scheduled_at' => Carbon::now()->addDays(20)->setHour(17)->setMinute(0),
                'capacity' => 10,
                'status' => 'published',
            ],
            [
                'title' => 'Frontend Mastery with Next.js & Tailwind CSS',
                'description' => 'Build high-performance web apps utilizing React Server Components, App Router, Server Actions, and rapid UI development with Tailwind CSS and Shadcn UI.',
                'scheduled_at' => Carbon::now()->addDays(25)->setHour(16)->setMinute(0),
                'capacity' => 18,
                'status' => 'published',
            ],
            [
                'title' => 'Machine Learning & Generative AI with PyTorch',
                'description' => 'Explore foundational neural networks, transfer learning, and fine-tuning Large Language Models using Python, PyTorch, and Hugging Face transformers.',
                'scheduled_at' => Carbon::now()->addDays(30)->setHour(15)->setMinute(0),
                'capacity' => 15,
                'status' => 'draft',
            ],
            [
                'title' => 'Cybersecurity & Ethical Hacking Essentials',
                'description' => 'Hands-on training covering penetration testing, network packet inspection with Wireshark, OWASP Top 10 web vulnerabilities, and defensive security measures.',
                'scheduled_at' => Carbon::now()->addDays(35)->setHour(18)->setMinute(30),
                'capacity' => 10,
                'status' => 'draft',
            ],
        ];

        foreach ($courses as $course) {
            Workshop::create($course);
        }
    }
}
