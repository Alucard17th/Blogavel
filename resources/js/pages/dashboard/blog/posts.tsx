import { Head, Link, useForm } from '@inertiajs/react';

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

type PostRow = {
    id: number;
    title: string;
    slug: string;
    status: string;
    published_at: string | null;
    category: { id: number; name: string } | null;
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
        title: 'Posts',
        href: '/dashboard/blog/posts',
    },
];

export default function DashboardBlogPosts({
    blogavel,
    posts,
}: {
    blogavel: BlogavelProps;
    posts: PostRow[];
}) {
    const actionForm = useForm({});

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Blog - Posts" />

            <div className="flex flex-1 flex-col gap-4 p-4">
                <Card className="py-0">
                    <CardHeader className="gap-2 pt-6">
                        <div className="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <CardTitle className="text-2xl">Posts</CardTitle>
                                <CardDescription>
                                    Manage posts (Blogavel admin)
                                </CardDescription>
                            </div>

                            <div className="flex items-center gap-2">
                                <Button asChild variant="outline">
                                    <a href="/dashboard/blog/posts/create">
                                        Create post
                                    </a>
                                </Button>
                                <Button asChild variant="link" className="px-0">
                                    <Link href={dashboard()}>Dashboard</Link>
                                </Button>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead className="w-[90px]">ID</TableHead>
                                    <TableHead>Title</TableHead>
                                    <TableHead className="hidden md:table-cell">
                                        Category
                                    </TableHead>
                                    <TableHead className="hidden md:table-cell">
                                        Status
                                    </TableHead>
                                    <TableHead className="hidden lg:table-cell">
                                        Published
                                    </TableHead>
                                    <TableHead className="w-[200px]" />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {posts.length === 0 ? (
                                    <TableRow>
                                        <TableCell colSpan={6}>
                                            <div className="py-6 text-center text-sm text-muted-foreground">
                                                No posts found.
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    posts.map((post) => (
                                        <TableRow key={post.id}>
                                            <TableCell className="font-mono text-xs">
                                                {post.id}
                                            </TableCell>
                                            <TableCell>
                                                <div className="font-medium">
                                                    {post.title}
                                                </div>
                                                <div className="text-xs text-muted-foreground">
                                                    {post.slug}
                                                </div>
                                            </TableCell>
                                            <TableCell className="hidden md:table-cell">
                                                {post.category?.name ?? '—'}
                                            </TableCell>
                                            <TableCell className="hidden md:table-cell">
                                                {post.status}
                                            </TableCell>
                                            <TableCell className="hidden lg:table-cell">
                                                {post.published_at
                                                    ? new Date(
                                                          post.published_at,
                                                      ).toLocaleDateString()
                                                    : '—'}
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-2">
                                                    <Button asChild variant="outline" size="sm">
                                                        <a
                                                            href={`/dashboard/blog/posts/${post.id}/edit`}
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
                                                                `/dashboard/blog/posts/${post.id}`,
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
