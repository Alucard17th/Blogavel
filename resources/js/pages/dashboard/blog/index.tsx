import { Head, Link } from '@inertiajs/react';

import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

import { MoreHorizontal } from 'lucide-react';

type BlogavelProps = {
    prefix: string;
    admin_prefix: string;
    admin_base: string;
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Blog',
        href: '/dashboard/blog',
    },
];

export default function DashboardBlogIndex({
    blogavel,
}: {
    blogavel: BlogavelProps;
}) {
    const links = [
        {
            title: 'Posts',
            description: 'Create, edit and publish posts',
            listHref: '/dashboard/blog/posts',
            createHref: '/dashboard/blog/posts/create',
        },
        {
            title: 'Categories',
            description: 'Manage post categories',
            listHref: '/dashboard/blog/categories',
            createHref: '/dashboard/blog/categories/create',
        },
        {
            title: 'Tags',
            description: 'Manage post tags',
            listHref: '/dashboard/blog/tags',
            createHref: '/dashboard/blog/tags/create',
        },
        {
            title: 'Media',
            description: 'Upload and manage images',
            listHref: '/dashboard/blog/media',
            createHref: '/dashboard/blog/media',
        },
        {
            title: 'Comments',
            description: 'Moderate comments',
            listHref: '/dashboard/blog/comments',
            createHref: '/dashboard/blog/comments',
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Blog" />

            <div className="flex flex-1 flex-col gap-6 p-4">
                <div className="space-y-1">
                    <h1 className="text-2xl font-semibold">Blog management</h1>
                    <p className="text-sm text-muted-foreground">
                        Manage your blog content.
                    </p>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    {links.map((item) => (
                        <Card key={item.title} className="py-0">
                            <CardHeader className="pt-6">
                                <div className="flex items-center justify-between gap-3">
                                    <CardTitle>
                                        <a
                                            href={item.listHref}
                                            className="hover:underline"
                                        >
                                            {item.title}
                                        </a>
                                    </CardTitle>
                                    <DropdownMenu>
                                        <DropdownMenuTrigger asChild>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                className="h-8 w-8"
                                            >
                                                <MoreHorizontal />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem asChild>
                                                <a href={item.listHref}>View all</a>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem asChild>
                                                <a href={item.createHref}>Create</a>
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
                                <CardDescription>{item.description}</CardDescription>
                            </CardHeader>
                            <CardContent />
                        </Card>
                    ))}
                </div>

                <div className="flex items-center gap-3">
                    <Button asChild variant="link" className="px-0">
                        <Link href={dashboard()}>Back to dashboard</Link>
                    </Button>
                </div>
            </div>
        </AppLayout>
    );
}
