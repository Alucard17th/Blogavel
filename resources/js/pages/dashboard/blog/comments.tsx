import { Head, useForm } from '@inertiajs/react';

import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
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

type CommentRow = {
    id: number;
    status: string;
    author: string;
    email: string | null;
    content: string;
    post: { id: number; title: string } | null;
    created_at: string | null;
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
        title: 'Comments',
        href: '/dashboard/blog/comments',
    },
];

export default function DashboardBlogComments({
    blogavel,
    comments,
}: {
    blogavel: BlogavelProps;
    comments: CommentRow[];
}) {
    const actionForm = useForm({});

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Blog - Comments" />

            <div className="flex flex-1 flex-col gap-4 p-4">
                <Card className="py-0">
                    <CardHeader className="pt-6">
                        <CardTitle className="text-2xl">Comments</CardTitle>
                        <CardDescription>
                            Moderate comments (Blogavel admin)
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead className="w-[90px]">ID</TableHead>
                                    <TableHead>Comment</TableHead>
                                    <TableHead className="hidden md:table-cell">
                                        Post
                                    </TableHead>
                                    <TableHead className="hidden md:table-cell">
                                        Status
                                    </TableHead>
                                    <TableHead className="hidden lg:table-cell">
                                        Date
                                    </TableHead>
                                    <TableHead className="w-[220px]" />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {comments.length === 0 ? (
                                    <TableRow>
                                        <TableCell colSpan={6}>
                                            <div className="py-6 text-center text-sm text-muted-foreground">
                                                No comments found.
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ) : (
                                    comments.map((c) => (
                                        <TableRow key={c.id}>
                                            <TableCell className="font-mono text-xs">
                                                {c.id}
                                            </TableCell>
                                            <TableCell>
                                                <div className="font-medium">
                                                    {c.author}
                                                </div>
                                                <div className="text-xs text-muted-foreground line-clamp-2">
                                                    {c.content}
                                                </div>
                                            </TableCell>
                                            <TableCell className="hidden md:table-cell">
                                                {c.post?.title ?? '—'}
                                            </TableCell>
                                            <TableCell className="hidden md:table-cell">
                                                {c.status}
                                            </TableCell>
                                            <TableCell className="hidden lg:table-cell">
                                                {c.created_at
                                                    ? new Date(
                                                          c.created_at,
                                                      ).toLocaleString()
                                                    : '—'}
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-2">
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        disabled={actionForm.processing}
                                                        onClick={() =>
                                                            actionForm.post(
                                                                `/dashboard/blog/comments/${c.id}/approve`,
                                                            )
                                                        }
                                                    >
                                                        Approve
                                                    </Button>
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        disabled={actionForm.processing}
                                                        onClick={() =>
                                                            actionForm.post(
                                                                `/dashboard/blog/comments/${c.id}/spam`,
                                                            )
                                                        }
                                                    >
                                                        Spam
                                                    </Button>
                                                    <Button
                                                        size="sm"
                                                        variant="destructive"
                                                        disabled={actionForm.processing}
                                                        onClick={() =>
                                                            actionForm.delete(
                                                                `/dashboard/blog/comments/${c.id}`,
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
