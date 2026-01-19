import { Head, useForm } from '@inertiajs/react';

import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

type BlogavelProps = {
    admin_base: string;
};

type CategoryRow = {
    id: number;
    name: string;
    slug: string;
    posts_count: number;
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
    {
        title: 'Categories',
        href: '/dashboard/blog/categories',
    },
];

export default function DashboardBlogCategories({
    blogavel,
    categories,
}: {
    blogavel: BlogavelProps;
    categories: CategoryRow[];
}) {
    const actionForm = useForm({});

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Blog - Categories" />

            <div className="flex flex-1 flex-col gap-4 p-4">
                <Card className="py-0">
                    <CardHeader className="gap-2 pt-6">
                        <div className="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <CardTitle className="text-2xl">Categories</CardTitle>
                                <CardDescription>
                                    Manage categories (Blogavel admin)
                                </CardDescription>
                            </div>

                            <Button asChild variant="outline">
                                <a href="/dashboard/blog/categories/create">
                                    Create category
                                </a>
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead className="w-[90px]">ID</TableHead>
                                    <TableHead>Name</TableHead>
                                    <TableHead className="hidden md:table-cell">
                                        Slug
                                    </TableHead>
                                    <TableHead className="w-[120px] text-right">
                                        Posts
                                    </TableHead>
                                    <TableHead className="w-[200px]" />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {categories.length === 0 ? (
                                    <TableRow>
                                        <TableCell colSpan={5}>
                                            <div className="py-6 text-center text-sm text-muted-foreground">
                                                No categories found.
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    categories.map((cat) => (
                                        <TableRow key={cat.id}>
                                            <TableCell className="font-mono text-xs">
                                                {cat.id}
                                            </TableCell>
                                            <TableCell>
                                                <div className="font-medium">
                                                    {cat.name}
                                                </div>
                                            </TableCell>
                                            <TableCell className="hidden md:table-cell">
                                                <span className="font-mono text-xs">
                                                    {cat.slug}
                                                </span>
                                            </TableCell>
                                            <TableCell className="text-right">
                                                {cat.posts_count}
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-2">
                                                    <Button asChild variant="outline" size="sm">
                                                        <a
                                                            href={`/dashboard/blog/categories/${cat.id}/edit`}
                                                        >
                                                            Edit
                                                        </a>
                                                    </Button>
                                                    <Button
                                                        variant="destructive"
                                                        size="sm"
                                                        disabled={actionForm.processing}
                                                        onClick={() =>
                                                            actionForm.delete(
                                                                `/dashboard/blog/categories/${cat.id}`,
                                                            )
                                                        }
                                                    >
                                                        Delete
                                                    </Button>
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
